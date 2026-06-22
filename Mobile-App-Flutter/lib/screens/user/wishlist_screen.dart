import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../models/product.dart';
import 'product_detail_screen.dart';

class WishlistScreen extends StatefulWidget {
  const WishlistScreen({super.key});

  @override
  State<WishlistScreen> createState() => _WishlistScreenState();
}

class _WishlistScreenState extends State<WishlistScreen> {
  List<dynamic> wishlistItemsRaw = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadWishlist();
  }

  Future<void> _loadWishlist() async {
    final data = await ApiService.fetchWishlist();
    setState(() {
      wishlistItemsRaw = data;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    // Map raw items to products
    final List<Product> wishlistItems = wishlistItemsRaw.map((e) => Product.fromJson(e['product'])).toList();

    return Scaffold(
      backgroundColor: const Color(0xFF0F0C20),
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        title: const Text(
          "MY WISHLIST",
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
          : wishlistItems.isEmpty
          ? Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.favorite_border, size: 60, color: const Color(0xFF8B87B5).withOpacity(0.5)),
                  const SizedBox(height: 16),
                  const Text(
                    "Your Wishlist is empty!",
                    style: TextStyle(color: Color(0xFF8B87B5), fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 8),
                  const Text(
                    "Save garments you love to try them on later.",
                    style: TextStyle(color: Color(0xFF5B5785), fontSize: 12),
                  ),
                ],
              ),
            )
          : ListView.builder(
              padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 10),
              itemCount: wishlistItems.length,
              itemBuilder: (context, index) {
                final item = wishlistItems[index];
                return _buildWishlistItem(context, item);
              },
            ),
    );
  }

  Widget _buildWishlistItem(BuildContext context, Product item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
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
              height: 80,
              width: 80,
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
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  item.category,
                  style: const TextStyle(color: Color(0xFF8B87B5), fontSize: 11),
                ),
                const SizedBox(height: 8),
                Text(
                  "\$${item.price.toStringAsFixed(2)}",
                  style: const TextStyle(
                    color: Color(0xFFF27121),
                    fontSize: 14,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),

          // Actions
          Column(
            children: [
              IconButton(
                icon: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 20),
                onPressed: () {},
              ),
              IconButton(
                icon: const Icon(Icons.auto_awesome, color: Color(0xFFE94057), size: 20),
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => ProductDetailScreen(product: item),
                    ),
                  );
                },
              ),
            ],
          ),
        ],
      ),
    );
  }
}
