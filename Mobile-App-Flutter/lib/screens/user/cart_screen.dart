import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../models/product.dart';
import 'user_dashboard_screen.dart' show VDRTheme;

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  List<Map<String, dynamic>> cartDisplayItems = [];
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _loadCart();
  }

  Future<void> _loadCart() async {
    setState(() {
      isLoading = true;
      errorMessage = null;
    });

    if (ApiService.currentUserId == null) {
      setState(() {
        isLoading = false;
        errorMessage = "Session expired. Please log out and log in again.";
      });
      return;
    }

    try {
      // Step 1: Fetch raw cart items
      final rawCart = await ApiService.fetchCart();
      debugPrint("[CartScreen] rawCart length: ${rawCart.length}");

      if (rawCart.isEmpty) {
        setState(() {
          cartDisplayItems = [];
          isLoading = false;
        });
        return;
      }

      // Step 2: Fetch ALL products (we know this works — home screen uses it)
      final allProducts = await ApiService.fetchProducts();
      final Map<int, Product> productMap = {};
      for (var p in allProducts) {
        final pid = (p.id is int) ? p.id as int : int.tryParse(p.id.toString()) ?? 0;
        productMap[pid] = p;
      }
      debugPrint("[CartScreen] products loaded: ${productMap.length}");

      // Step 3: Match cart items with products
      final List<Map<String, dynamic>> result = [];
      for (var rawItem in rawCart) {
        final int cartId = _safeInt(rawItem['id']);
        final int productId = _safeInt(rawItem['product_id']);
        final int quantity = _safeInt(rawItem['quantity'], fallback: 1);

        // Try to get product from rawItem first, then fallback to productMap
        Product? product;

        if (rawItem['product'] != null && rawItem['product'] is Map) {
          try {
            product = Product.fromJson(Map<String, dynamic>.from(rawItem['product']));
            if (product.name.isEmpty && product.price == 0) product = null;
          } catch (_) {
            product = null;
          }
        }

        // Fallback: use the product from our fetched list
        product ??= productMap[productId];

        if (product != null) {
          result.add({
            'cartId': cartId,
            'product': product,
            'quantity': quantity,
          });
        } else {
          debugPrint("[CartScreen] Product not found for product_id=$productId");
        }
      }

      setState(() {
        cartDisplayItems = result;
        isLoading = false;
      });
    } catch (e) {
      debugPrint("[CartScreen] Error: $e");
      setState(() {
        isLoading = false;
        errorMessage = "Failed to load cart: $e";
      });
    }
  }

  int _safeInt(dynamic val, {int fallback = 0}) {
    if (val is int) return val;
    if (val is num) return val.toInt();
    if (val is String) return int.tryParse(val) ?? fallback;
    return fallback;
  }

  @override
  Widget build(BuildContext context) {
    double subtotal = 0;
    for (var item in cartDisplayItems) {
      final Product p = item['product'];
      final int qty = item['quantity'];
      subtotal += p.price * qty;
    }
    final double delivery = subtotal > 0 ? 5.00 : 0.00;
    final double total = subtotal + delivery;

    return Scaffold(
      backgroundColor: VDRTheme.bgDark,
      appBar: AppBar(
        backgroundColor: VDRTheme.bgDark,
        elevation: 0,
        centerTitle: true,
        title: ShaderMask(
          shaderCallback: (b) => VDRTheme.mainGradient.createShader(b),
          child: const Text(
            "MY CART",
            style: TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.bold,
              letterSpacing: 2.0,
              fontSize: 18,
            ),
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: VDRTheme.textSub),
            onPressed: _loadCart,
            tooltip: "Refresh",
          ),
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: VDRTheme.primary))
          : errorMessage != null
              ? _buildErrorState()
              : cartDisplayItems.isEmpty
                  ? _buildEmptyState()
                  : _buildCartBody(subtotal, delivery, total),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(28),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.redAccent.withOpacity(0.1),
                shape: BoxShape.circle,
                border: Border.all(color: Colors.redAccent.withOpacity(0.3)),
              ),
              child: const Icon(Icons.error_outline, color: Colors.redAccent, size: 48),
            ),
            const SizedBox(height: 20),
            Text(
              errorMessage!,
              textAlign: TextAlign.center,
              style: const TextStyle(color: VDRTheme.textSub, fontSize: 14, height: 1.6),
            ),
            const SizedBox(height: 24),
            Container(
              decoration: BoxDecoration(
                gradient: VDRTheme.mainGradient,
                borderRadius: BorderRadius.circular(14),
                boxShadow: [
                  BoxShadow(color: VDRTheme.primary.withOpacity(0.4), blurRadius: 14, offset: const Offset(0, 5)),
                ],
              ),
              child: ElevatedButton.icon(
                onPressed: _loadCart,
                icon: const Icon(Icons.refresh_rounded, color: Colors.white),
                label: const Text("Try Again", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.transparent,
                  shadowColor: Colors.transparent,
                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 14),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(28),
            decoration: BoxDecoration(
              color: VDRTheme.bgCard2,
              shape: BoxShape.circle,
              border: Border.all(color: VDRTheme.border),
              boxShadow: [BoxShadow(color: VDRTheme.primary.withOpacity(0.08), blurRadius: 20)],
            ),
            child: const Icon(Icons.shopping_bag_outlined, size: 56, color: VDRTheme.textMuted),
          ),
          const SizedBox(height: 22),
          const Text(
            "Your Cart is Empty!",
            style: TextStyle(color: Color(0xFF2F2F2F), fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 10),
          const Text(
            "Browse our exclusive collection\nand add dresses to your cart.",
            textAlign: TextAlign.center,
            style: TextStyle(color: VDRTheme.textMuted, fontSize: 13, height: 1.6),
          ),
          const SizedBox(height: 24),
          TextButton.icon(
            onPressed: _loadCart,
            icon: const Icon(Icons.refresh_rounded, color: VDRTheme.textSub),
            label: const Text("Refresh", style: TextStyle(color: VDRTheme.textSub)),
          ),
        ],
      ),
    );
  }

  Widget _buildCartBody(double subtotal, double delivery, double total) {
    return Stack(
      children: [
        ListView.builder(
          padding: const EdgeInsets.only(left: 18, right: 18, top: 14, bottom: 270),
          itemCount: cartDisplayItems.length,
          itemBuilder: (context, index) => _buildCartItem(cartDisplayItems[index]),
        ),
        Positioned(
          bottom: 0, left: 0, right: 0,
          child: Container(
            padding: const EdgeInsets.fromLTRB(22, 18, 22, 28),
            decoration: BoxDecoration(
              color: VDRTheme.bgCard,
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(28),
                topRight: Radius.circular(28),
              ),
              border: Border(
                top: BorderSide(color: VDRTheme.borderGlow.withOpacity(0.4), width: 1),
              ),
              boxShadow: [
                BoxShadow(
                  color: VDRTheme.primary.withOpacity(0.1),
                  blurRadius: 24,
                  offset: const Offset(0, -8),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              mainAxisSize: MainAxisSize.min,
              children: [
                _buildSummaryRow("Subtotal", "\$${subtotal.toStringAsFixed(2)}"),
                const SizedBox(height: 6),
                _buildSummaryRow("Delivery Fee", "\$${delivery.toStringAsFixed(2)}"),
                Divider(color: VDRTheme.border, height: 22, thickness: 1),
                _buildSummaryRow("Total", "\$${total.toStringAsFixed(2)}", isTotal: true),
                const SizedBox(height: 18),
                Container(
                  height: 54,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16),
                    gradient: VDRTheme.mainGradient,
                    boxShadow: [
                      BoxShadow(
                        color: VDRTheme.primary.withOpacity(0.5),
                        blurRadius: 18,
                        offset: const Offset(0, 6),
                      ),
                    ],
                  ),
                  child: ElevatedButton(
                    onPressed: () async {
                      final messenger = ScaffoldMessenger.of(context);
                      final res = await ApiService.checkout();
                      if (res['status'] == 'success') {
                        messenger.showSnackBar(SnackBar(
                          content: const Row(children: [
                            Icon(Icons.check_circle, color: Colors.white),
                            SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                "Order placed! Thank you for purchasing. 🎉",
                                style: TextStyle(color: Colors.white, fontWeight: FontWeight.w500),
                              ),
                            ),
                          ]),
                          backgroundColor: VDRTheme.borderGlow,
                          behavior: SnackBarBehavior.floating,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ));
                        _loadCart();
                      } else {
                        messenger.showSnackBar(SnackBar(
                          content: Row(children: [
                            const Icon(Icons.error_outline, color: Colors.white, size: 16),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                res['message'] ?? "Checkout failed.",
                                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w500),
                              ),
                            ),
                          ]),
                          backgroundColor: Colors.redAccent,
                          behavior: SnackBarBehavior.floating,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                        ));
                      }
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.transparent,
                      shadowColor: Colors.transparent,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    child: const Text(
                      "PROCEED TO CHECKOUT  ✨",
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 0.8,
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildCartItem(Map<String, dynamic> displayItem) {
    final Product item = displayItem['product'];
    final int cartId = displayItem['cartId'];
    final int quantity = displayItem['quantity'];

    String imageUrl = item.imageUrl;
    if (imageUrl.isNotEmpty && !imageUrl.startsWith('http')) {
      imageUrl = "${ApiService.baseUrl.replaceAll('/api', '')}$imageUrl";
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: VDRTheme.bgCard2,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: VDRTheme.border),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 10, offset: const Offset(0, 4)),
        ],
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          // Product Image
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: imageUrl.isNotEmpty
                ? Image.network(
                    imageUrl,
                    height: 85,
                    width: 85,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => _imagePlaceholder(),
                  )
                : _imagePlaceholder(),
          ),
          const SizedBox(width: 14),

          // Details
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                // Name + Delete button in same row
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        item.name,
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(color: Color(0xFF2F2F2F), fontSize: 14, fontWeight: FontWeight.bold),
                      ),
                    ),
                    GestureDetector(
                      onTap: () async {
                        if (cartId == 0) return;
                        await ApiService.removeFromCart(cartId);
                        _loadCart();
                      },
                      child: Container(
                        padding: const EdgeInsets.all(5),
                        decoration: BoxDecoration(
                          color: Colors.redAccent.withOpacity(0.1),
                          shape: BoxShape.circle,
                          border: Border.all(color: Colors.redAccent.withOpacity(0.3)),
                        ),
                        child: const Icon(Icons.delete_outline_rounded, color: Colors.redAccent, size: 16),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 3),
                Text(
                  item.category,
                  style: const TextStyle(color: VDRTheme.textMuted, fontSize: 11),
                ),
                const SizedBox(height: 5),
                Text(
                  "\$${(item.price * quantity).toStringAsFixed(2)}",
                  style: const TextStyle(color: Color(0xFFD4AF37), fontSize: 15, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 8),
                // Qty Controls — always show minus/qty/plus
                Row(
                  children: [
                    _qtyButton(
                      icon: Icons.remove_rounded,
                      color: quantity > 1 ? const Color(0xFF2F2F2F) : const Color(0xFFCCCCCC),
                      onTap: () async {
                        if (cartId == 0 || quantity <= 1) return;
                        await ApiService.updateCartQuantity(cartId, quantity - 1);
                        _loadCart();
                      },
                    ),
                    Container(
                      margin: const EdgeInsets.symmetric(horizontal: 10),
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFFF5F5F5),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: const Color(0xFFEAEAEA)),
                      ),
                      child: Text(
                        "$quantity",
                        style: const TextStyle(color: Color(0xFF2F2F2F), fontWeight: FontWeight.bold, fontSize: 15),
                      ),
                    ),
                    _qtyButton(
                      icon: Icons.add_rounded,
                      color: VDRTheme.primary,
                      onTap: () async {
                        if (cartId == 0) return;
                        await ApiService.updateCartQuantity(cartId, quantity + 1);
                        _loadCart();
                      },
                    ),
                  ],
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }


  Widget _qtyButton({required IconData icon, required Color color, required VoidCallback onTap}) {
    return GestureDetector(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.all(7),
        child: Icon(icon, color: color, size: 18),
      ),
    );
  }

  Widget _imagePlaceholder() {
    return Container(
      height: 80,
      width: 80,
      decoration: BoxDecoration(
        color: VDRTheme.bgCard,
        borderRadius: BorderRadius.circular(12),
      ),
      child: const Icon(Icons.image_outlined, color: VDRTheme.textMuted),
    );
  }

  Widget _buildSummaryRow(String label, String val, {bool isTotal = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            color: isTotal ? const Color(0xFF2F2F2F) : VDRTheme.textSub,
            fontSize: isTotal ? 16 : 13,
            fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
          ),
        ),
        isTotal
            ? Text(
                val,
                style: const TextStyle(color: Color(0xFFD4AF37), fontSize: 20, fontWeight: FontWeight.bold),
              )
            : Text(val, style: const TextStyle(color: Color(0xFF2F2F2F), fontSize: 13)),
      ],
    );
  }
}
