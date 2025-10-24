<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Item;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\Inventory;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class POS extends Component
{
    public $items = [];
    public $customers = [];
    public $paymentMethods = [];
    public $search = '';
    public $cart = [];
    public $selectedCustomer = '';
    public $selectedPaymentMethod = '';
    public $discount = 0;
    public $amountPaid = 0;
    public $changeGiven = 0;

    // Modal de confirmation
    public $showConfirmationModal = false;
    public $processingPayment = false;

    public function mount()
    {
        $this->loadItems();
        $this->loadCustomers();
        $this->loadPaymentMethods();
    }

    public function updatedSearch()
    {
        $this->loadItems();
    }

    public function loadItems()
    {
        $query = Item::query()
            ->where('status', 'active')
            ->with(['inventory']) // Charger l'inventaire
            ->orderBy('name');

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }

        $this->items = $query->get();
    }

    public function loadCustomers()
    {
        $this->customers = Customer::orderBy('name')->get();
    }

    public function loadPaymentMethods()
    {
        $this->paymentMethods = PaymentMethod::orderBy('name')->get();
    }

    // Propriétés calculées
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) =>
            $item['price'] * $item['quantity']
        );
    }

    public function getTaxProperty()
    {
        return $this->subtotal * 0.15; // 15% TVA
    }

    public function getTotalBeforeDiscountProperty()
    {
        return $this->subtotal + $this->tax;
    }

    public function getFinalTotalProperty()
    {
        return max($this->totalBeforeDiscount - $this->discount, 0);
    }

    public function getCartItemsCountProperty()
    {
        return collect($this->cart)->sum('quantity');
    }

    public function updatedAmountPaid()
    {
        $this->changeGiven = max($this->amountPaid - $this->finalTotal, 0);
    }

    public function updatedDiscount()
    {
        $this->updatedAmountPaid();
    }

    // Gestion du panier
    public function addToCart($itemId)
    {
        $item = Item::with('inventory')->find($itemId);

        if (!$item) {
            $this->showErrorNotification('Product not found!');
            return;
        }

        // Vérifier le stock via la relation inventory
        $availableStock = $item->inventory ? $item->inventory->quantity : 0;

        if ($availableStock <= 0) {
            $this->showErrorNotification('Product is out of stock!');
            return;
        }

        $currentQuantity = $this->cart[$itemId]['quantity'] ?? 0;

        if ($currentQuantity + 1 > $availableStock) {
            $this->showWarningNotification("Only {$availableStock} units available in stock!");
            return;
        }

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity']++;
        } else {
            $this->cart[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1,
                'sku' => $item->sku,
            ];
        }

        $this->dispatch('item-added-to-cart');
    }

    public function removeFromCart($itemId)
    {
        if (isset($this->cart[$itemId])) {
            unset($this->cart[$itemId]);
        }
    }

    public function updateCartQuantity($itemId, $newQuantity)
    {
        if (!isset($this->cart[$itemId])) return;

        $item = Item::with('inventory')->find($itemId);
        $availableStock = $item->inventory ? $item->inventory->quantity : 0;

        if ($newQuantity <= 0) {
            $this->removeFromCart($itemId);
            return;
        }

        if ($newQuantity > $availableStock) {
            $this->showWarningNotification("Only {$availableStock} units available!");
            return;
        }

        $this->cart[$itemId]['quantity'] = $newQuantity;
    }

    public function incrementQuantity($itemId)
    {
        $this->updateCartQuantity($itemId, ($this->cart[$itemId]['quantity'] ?? 0) + 1);
    }

    public function decrementQuantity($itemId)
    {
        $this->updateCartQuantity($itemId, ($this->cart[$itemId]['quantity'] ?? 0) - 1);
    }

    public function clearCart()
    {
        $this->cart = [];
    }

    // Modal de confirmation
    public function openConfirmationModal()
    {
        $validationErrors = $this->validatePayment();

        if (!empty($validationErrors)) {
            $this->showErrorNotification($validationErrors[0]);
            return;
        }

        $this->showConfirmationModal = true;
    }

    public function closeConfirmationModal()
    {
        $this->showConfirmationModal = false;
    }

    // Validation du paiement
    private function validatePayment()
    {
        $errors = [];

        if (empty($this->cart)) {
            $errors[] = 'Cart is empty!';
        }

        if (!$this->selectedCustomer) {
            $errors[] = 'Please select a customer.';
        }

        if (!$this->selectedPaymentMethod) {
            $errors[] = 'Please select a payment method.';
        }

        if ($this->amountPaid < $this->finalTotal) {
            $errors[] = 'Insufficient amount paid.';
        }

        // Vérification du stock pour tous les articles du panier
        foreach ($this->cart as $itemId => $cartItem) {
            $item = Item::with('inventory')->find($itemId);
            $availableStock = $item->inventory ? $item->inventory->quantity : 0;

            if ($availableStock < $cartItem['quantity']) {
                $errors[] = "Insufficient stock for {$cartItem['name']}. Available: {$availableStock}";
            }
        }

        return $errors;
    }

    // Confirmation du paiement
    public function confirmPayment()
    {
        $this->processingPayment = true;

        try {
            DB::transaction(function () {
                // Créer la vente
                $sale = Sale::create([
                    'customer_id' => $this->selectedCustomer,
                    'payment_method_id' => $this->selectedPaymentMethod,
                    'total' => $this->finalTotal,
                    'paid_amount' => $this->amountPaid,
                    'discount' => $this->discount,
                    // Note: change_given n'est pas dans $fillable, on le calcule dynamiquement
                ]);

                // Créer les items de vente et mettre à jour l'inventaire
                foreach ($this->cart as $itemId => $cartItem) {
                    // Créer l'item de vente
                    SalesItem::create([
                        'sale_id' => $sale->id,
                        'item_id' => $itemId,
                        'quantity' => $cartItem['quantity'],
                        'price' => $cartItem['price'],
                    ]);

                    // Mettre à jour l'inventaire
                    $inventory = Inventory::where('item_id', $itemId)->first();

                    if ($inventory) {
                        $inventory->decrement('quantity', $cartItem['quantity']);

                        // Optionnel: Mettre à jour le statut de l'item si stock épuisé
                        if ($inventory->quantity <= 0) {
                            Item::where('id', $itemId)->update(['status' => 'inactive']);
                        }
                    }
                }
            });

            // Notification de succès
            $this->showSuccessNotification(
                'Payment Successful!',
                "Sale has been recorded successfully. Change: {$this->changeGiven} FCFA"
            );

            // Réinitialisation
            $this->resetCheckout();

        } catch (\Exception $e) {
            // Notification d'erreur
            $this->showErrorNotification('Payment Error: ' . $e->getMessage());
            logger()->error('POS Payment Error: ' . $e->getMessage());
        } finally {
            $this->processingPayment = false;
            $this->showConfirmationModal = false;
        }
    }

    // Réinitialisation
    private function resetCheckout()
    {
        $this->reset([
            'cart',
            'discount',
            'amountPaid',
            'changeGiven',
            'selectedCustomer',
            'selectedPaymentMethod'
        ]);

        // Recharger les items pour mettre à jour les stocks affichés
        $this->loadItems();
    }

    // Méthodes utilitaires pour les notifications
    private function showSuccessNotification($title, $message = '')
    {
        Notification::make()
            ->title($title)
            ->success()
            ->body($message)
            ->send();
    }

    private function showErrorNotification($message)
    {
        Notification::make()
            ->title('Error')
            ->danger()
            ->body($message)
            ->send();
    }

    private function showWarningNotification($message)
    {
        Notification::make()
            ->title('Warning')
            ->warning()
            ->body($message)
            ->send();
    }

    // Méthodes pour les actions rapides
    public function applyPercentageDiscount($percentage)
    {
        $this->discount = ($this->totalBeforeDiscount * $percentage) / 100;
        $this->updatedAmountPaid();
    }

    public function setExactAmount()
    {
        $this->amountPaid = $this->finalTotal;
        $this->updatedAmountPaid();
    }

    public function render()
    {
        return view('livewire.p-o-s');
    }
}
