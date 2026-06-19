import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../models/product.dart';

class CartScreen extends StatefulWidget {
  const CartScreen({super.key});

  @override
  State<CartScreen> createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  List<dynamic> cartItemsRaw = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadCart();
  }

  Future<void> _loadCart() async {
    final data = await ApiService.fetchCart();
    setState(() {
      cartItemsRaw = data;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    // Map raw cart items to products for calculation
    final List<Product> cartItems = cartItemsRaw.map((e) => Product.fromJson(e['product'])).toList();
    final double subtotal = cartItems.fold(0, (sum, item) => sum + item.price);
    final double tax = subtotal * 0.08;
    final double delivery = subtotal > 0 ? 10.00 : 0.00;
    final double total = subtotal + tax + delivery;

    return Scaffold(
      backgroundColor: const Color(0xFF0F0C20),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: const Text(
          "MY CART",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            letterSpacing: 1.0,
            fontSize: 18,
          ),
        ),
      ),
      body: isLoading 
          ? const Center(child: CircularProgressIndicator(color: Color(0xFFE94057)))
          : cartItems.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.shopping_bag_outlined, size: 60, color: const Color(0xFF8B87B5).withOpacity(0.5)),
                  const SizedBox(height: 16),
                  const Text(
                    "Your Cart is empty!",
                    style: TextStyle(color: Color(0xFF8B87B5), fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    "Select products and add them here to purchase.",
                    style: TextStyle(color: Color(0xFF5B5785), fontSize: 12),
                  ),
                ],
              ),
            )
          : Stack(
              children: [
                // Scrollable cart items list
                ListView.builder(
                  padding: const EdgeInsets.only(left: 20.0, right: 20.0, top: 10, bottom: 250),
                  itemCount: cartItems.length,
                  itemBuilder: (context, index) {
                    final item = cartItems[index];
                    return _buildCartItem(item);
                  },
                ),

                // Fixed Summary & Checkout Panel
                Positioned(
                  bottom: 0,
                  left: 0,
                  right: 0,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 20),
                    decoration: BoxDecoration(
                      color: const Color(0xFF15102A),
                      borderRadius: const BorderRadius.only(
                        topLeft: Radius.circular(24),
                        topRight: Radius.circular(24),
                      ),
                      border: Border(
                        top: BorderSide(color: const Color(0xFF322E54).withOpacity(0.5), width: 1),
                      ),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        _buildSummaryRow("Subtotal", "\$${subtotal.toStringAsFixed(2)}"),
                        const SizedBox(height: 8),
                        _buildSummaryRow("Sales Tax (8%)", "\$${tax.toStringAsFixed(2)}"),
                        const SizedBox(height: 8),
                        _buildSummaryRow("Delivery Fee", "\$${delivery.toStringAsFixed(2)}"),
                        const Divider(color: Color(0xFF322E54), height: 24, thickness: 1),
                        _buildSummaryRow("Total Price", "\$${total.toStringAsFixed(2)}", isTotal: true),
                        const SizedBox(height: 20),

                        // Checkout Button
                        Container(
                          height: 52,
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(14),
                            gradient: const LinearGradient(
                              colors: [Color(0xFF8A2387), Color(0xFFE94057), Color(0xFFF27121)],
                            ),
                          ),
                          child: ElevatedButton(
                            onPressed: () {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(
                                  content: Text("Order simulated successfully! Thank you for purchasing."),
                                  backgroundColor: Colors.green,
                                ),
                              );
                            },
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.transparent,
                              shadowColor: Colors.transparent,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(14),
                              ),
                            ),
                            child: const Text(
                              "PROCEED TO CHECKOUT",
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                letterSpacing: 1.0,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildCartItem(Product item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: const Color(0xFF1E1B38),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFF322E54)),
      ),
      child: Row(
        children: [
          // Image
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: Image.network(
              item.imageUrl.startsWith('http') ? item.imageUrl : "${ApiService.baseUrl.replaceAll('/api', '')}${item.imageUrl}",
              height: 70,
              width: 70,
              fit: BoxFit.cover,
              errorBuilder: (_, __, ___) => const Icon(Icons.broken_image, color: Colors.grey),
            ),
          ),
          const SizedBox(width: 16),

          // Details
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  item.name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  "\$${item.price.toStringAsFixed(2)}",
                  style: const TextStyle(
                    color: Color(0xFFF27121),
                    fontSize: 13,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),

          // Quantity controls
          Row(
            children: [
              IconButton(
                icon: const Icon(Icons.remove_circle_outline, color: Color(0xFF8B87B5), size: 20),
                onPressed: () {},
              ),
              const Text("1", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
              IconButton(
                icon: const Icon(Icons.add_circle_outline, color: Color(0xFF8B87B5), size: 20),
                onPressed: () {},
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, String val, {bool isTotal = false}) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            color: isTotal ? Colors.white : const Color(0xFF8B87B5),
            fontSize: isTotal ? 15 : 13,
            fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
          ),
        ),
        Text(
          val,
          style: TextStyle(
            color: isTotal ? const Color(0xFFF27121) : Colors.white,
            fontSize: isTotal ? 18 : 13,
            fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
          ),
        ),
      ],
    );
  }
}
