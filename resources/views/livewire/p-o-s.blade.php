<div class="flex flex-col lg:flex-row gap-6 p-6 bg-gradient-to-br from-gray-900 to-gray-950 text-white min-h-screen">

    {{-- SECTION PRODUITS AMÉLIORÉE --}}
    <div class="flex-1">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-purple-400 bg-clip-text text-transparent">Products</h2>
                <p class="text-gray-400 text-sm mt-1">Browse and add items to cart</p>
            </div>

            {{-- Recherche avec icône --}}
            <div class="relative w-full sm:w-64">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search products by name or SKU..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-800 border border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" />
            </div>
        </div>

        {{-- Indicateur de recherche --}}
        @if($search)
            <div class="mb-4 px-4 py-2 bg-gray-800 rounded-lg border-l-4 border-indigo-500">
                <p class="text-sm text-gray-300">
                    Showing results for: <span class="text-indigo-300">"{{ $search }}"</span>
                    <span class="text-gray-500">• {{ count($items) }} items found</span>
                </p>
            </div>
        @endif

        {{-- Grille de produits améliorée --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($items as $item)
                <div class="bg-gradient-to-b from-gray-800 to-gray-900 rounded-2xl p-4 flex flex-col border border-gray-700 hover:border-indigo-500/30 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300 group">
                    {{-- Image avec badge de stock --}}
                    <div class="relative w-full h-32 bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl flex items-center justify-center text-gray-400 group-hover:from-gray-600 group-hover:to-gray-700 transition-all duration-300 mb-3 overflow-hidden">
                        @if($item->image)
                            <img src="{{ $item->image }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        @endif

                        {{-- Badge de stock CORRIGÉ --}}
                        @if($item->inventory)
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-xs rounded-full {{ $item->inventory->quantity > 10 ? 'bg-green-500/20 text-green-300' : ($item->inventory->quantity > 0 ? 'bg-orange-500/20 text-orange-300' : 'bg-red-500/20 text-red-300') }}">
                                    {{ $item->inventory->quantity }} in stock
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Informations produit --}}
                    <div class="flex-1">
                        <h3 class="font-semibold text-white group-hover:text-indigo-300 transition-colors line-clamp-1">{{ $item->name }}</h3>
                        <p class="text-xs text-gray-400 mt-1">SKU: {{ $item->sku }}</p>

                        {{-- Prix --}}
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-lg font-bold text-indigo-400">{{ number_format($item->price, 0, ',', ' ') }} FCFA</span>

                            {{-- Quantité dans le panier --}}
                            @if(isset($cart[$item->id]))
                                <span class="px-2 py-1 bg-indigo-500/20 text-indigo-300 text-xs rounded-full">
                                    {{ $cart[$item->id]['quantity'] }} in cart
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Bouton Ajouter --}}
                    <button wire:click="addToCart({{ $item->id }})"
                        @class([
                            'mt-3 w-full py-2.5 rounded-xl font-medium transition-all duration-200 transform flex items-center justify-center gap-2',
                            'bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-indigo-500/25' =>
                                $item->inventory && $item->inventory->quantity > 0,
                            'bg-gray-600 cursor-not-allowed opacity-50' =>
                                !$item->inventory || $item->inventory->quantity <= 0
                        ])>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        @if(!$item->inventory || $item->inventory->quantity <= 0)
                            Out of Stock
                        @else
                            Add to Cart
                        @endif
                    </button>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="max-w-md mx-auto">
                        <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-300 mb-2">No products found</h3>
                        <p class="text-gray-500 text-sm">
                            @if($search)
                                Try adjusting your search terms
                            @else
                                No products available at the moment
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- SECTION CHECKOUT AMÉLIORÉE --}}
    <div class="w-full lg:w-96 xl:w-[28rem]">
        <div class="bg-gradient-to-b from-gray-800 to-gray-900 rounded-2xl p-6 border border-gray-700 sticky top-6 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-green-400 to-cyan-400 bg-clip-text text-transparent">Checkout</h2>
                @if(!empty($cart))
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 text-sm rounded-full border border-indigo-500/30">
                            {{ $this->cartItemsCount }} {{ Str::plural('item', $this->cartItemsCount) }}
                        </span>
                        <button wire:click="clearCart"
                                class="p-1.5 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>

            @if(empty($cart))
                {{-- Panier vide --}}
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-300 mb-2">Your cart is empty</h3>
                    <p class="text-gray-500 text-sm">Add some products to get started</p>
                </div>
            @else
                {{-- Liste du panier avec scroll --}}
                <div class="space-y-3 mb-6 max-h-80 overflow-y-auto pr-2">
                    @foreach($cart as $item)
                        <div class="flex items-center justify-between bg-gray-800/50 p-3 rounded-xl border border-gray-700 hover:border-gray-600 transition-all group">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                {{-- Image miniature --}}
                                <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>

                                {{-- Détails --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-white truncate">{{ $item['name'] }}</p>
                                    <p class="text-sm text-gray-400">
                                        {{ number_format($item['price'], 0, ',', ' ') }} FCFA
                                    </p>
                                </div>
                            </div>

                            {{-- Contrôles de quantité --}}
                            <div class="flex items-center gap-2">
                                <button wire:click="decrementQuantity({{ $item['id'] }})"
                                        class="w-8 h-8 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>

                                <span class="w-8 text-center font-medium text-white">
                                    {{ $item['quantity'] }}
                                </span>

                                <button wire:click="incrementQuantity({{ $item['id'] }})"
                                        class="w-8 h-8 flex items-center justify-center bg-gray-700 hover:bg-gray-600 rounded-lg transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </button>
                            </div>

                            {{-- Sous-total et bouton supprimer --}}
                            <div class="flex items-center gap-3">
                                <span class="text-white font-semibold text-sm whitespace-nowrap">
                                    {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA
                                </span>
                                <button wire:click="removeFromCart({{ $item['id'] }})"
                                        class="p-1.5 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-all group/remove">
                                    <svg class="w-4 h-4 group-hover/remove:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Actions rapides --}}
                <div class="flex gap-2 mb-4">
                    <button wire:click="setExactAmount"
                            class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded-lg transition-all">
                        Exact Amount
                    </button>
                    <button wire:click="applyPercentageDiscount(10)"
                            class="flex-1 px-3 py-2 bg-orange-600 hover:bg-orange-500 text-white text-sm rounded-lg transition-all">
                        10% Off
                    </button>
                </div>

                {{-- Champs de formulaire --}}
                <div class="space-y-4 mb-6">
                    {{-- Client --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Customer</label>
                        <select wire:model="selectedCustomer"
                                class="w-full bg-gray-800 border border-gray-600 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="" class="text-gray-500">Select a customer</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" class="text-white">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Méthode de paiement --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Payment Method</label>
                        <select wire:model="selectedPaymentMethod"
                                class="w-full bg-gray-800 border border-gray-600 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="" class="text-gray-500">Select payment method</option>
                            @foreach($paymentMethods as $pm)
                                <option value="{{ $pm->id }}" class="text-white">{{ $pm->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Remise --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Discount (FCFA)</label>
                        <input type="number"
                               wire:model="discount"
                               min="0"
                               max="{{ $this->totalBeforeDiscount }}"
                               class="w-full bg-gray-800 border border-gray-600 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all"
                               placeholder="0" />
                    </div>

                    {{-- Montant payé --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Amount Paid (FCFA)</label>
                        <input type="number"
                               wire:model.live="amountPaid"
                               min="0"
                               placeholder="Enter amount received"
                               class="w-full bg-gray-800 border border-gray-600 rounded-xl p-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all" />
                    </div>
                </div>

                {{-- Résumé financier --}}
                <div class="bg-gray-800/50 rounded-xl p-4 border border-gray-700 space-y-3 mb-6">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Subtotal</span>
                        <span class="text-white">{{ number_format($this->subtotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Tax (15%)</span>
                        <span class="text-white">{{ number_format($this->tax, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Total before discount</span>
                        <span class="text-white">{{ number_format($this->totalBeforeDiscount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($discount > 0)
                        <div class="flex justify-between items-center text-sm border-t border-gray-700 pt-2">
                            <span class="text-red-400">Discount</span>
                            <span class="text-red-400">- {{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center text-lg font-bold border-t border-gray-700 pt-3">
                        <span class="text-white">Final Total</span>
                        <span class="text-green-400">{{ number_format($this->finalTotal, 0, ',', ' ') }} FCFA</span>
                    </div>

                    {{-- Montant rendu --}}
                    @if($this->changeGiven > 0)
                        <div class="flex justify-between items-center text-sm border-t border-gray-700 pt-2">
                            <span class="text-cyan-400">Change Given</span>
                            <span class="text-cyan-400">{{ number_format($this->changeGiven, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endif
                </div>

                {{-- Bouton de paiement --}}
                <button wire:click="openConfirmationModal"
                    @class([
                        'w-full py-4 rounded-xl font-bold text-white transition-all duration-200 transform shadow-lg flex items-center justify-center gap-3',
                        'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 hover:scale-[1.02] active:scale-[0.98] hover:shadow-green-500/25' =>
                            $this->finalTotal > 0 && $this->amountPaid >= $this->finalTotal && $this->selectedCustomer && $this->selectedPaymentMethod,
                        'bg-gray-600 cursor-not-allowed opacity-50' =>
                            !($this->finalTotal > 0 && $this->amountPaid >= $this->finalTotal && $this->selectedCustomer && $this->selectedPaymentMethod)
                    ])>
                    @if($processingPayment)
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2"></path>
                        </svg>
                        Processing...
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Process Payment
                        <span class="text-sm opacity-80">
                            ({{ number_format($this->finalTotal, 0, ',', ' ') }} FCFA)
                        </span>
                    @endif
                </button>
            @endif
        </div>
    </div>

    {{-- Modal de confirmation --}}
    @if($showConfirmationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-800 rounded-2xl p-6 w-full max-w-md border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4">Confirm Payment</h2>

                <p class="text-gray-300 mb-4">Are you sure you want to process this payment?</p>

                <div class="bg-gray-700/50 p-4 rounded-xl mb-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Final Total:</span>
                        <span class="text-white font-semibold">{{ number_format($this->finalTotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Amount Paid:</span>
                        <span class="text-white font-semibold">{{ number_format($amountPaid, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-600 pt-2">
                        <span class="text-cyan-400">Change:</span>
                        <span class="text-cyan-400 font-semibold">{{ number_format($changeGiven, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        wire:click="closeConfirmationModal"
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 text-gray-300 hover:text-white border border-gray-600 hover:border-gray-500 rounded-xl transition-all"
                        :disabled="$processingPayment">
                        Cancel
                    </button>
                    <button
                        wire:click="confirmPayment"
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-500 text-white rounded-xl font-semibold transition-all flex items-center gap-2"
                        :disabled="$processingPayment">
                        @if($processingPayment)
                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v4m0 12v4m8-10h-4M6 12H2"></path>
                            </svg>
                            Processing...
                        @else
                            Confirm Payment
                        @endif
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
