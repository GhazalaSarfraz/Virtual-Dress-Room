import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../../models/product.dart';
import 'product_detail_screen.dart';
import 'user_dashboard_screen.dart' show VDRTheme;

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
    setState(() => isLoading = true);
    final data = await ApiService.fetchWishlist();
    setState(() {
      wishlistItemsRaw = data;
      isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    final List<dynamic> wishlistItems = wishlistItemsRaw;

    return Scaffold(
      backgroundColor: VDRTheme.bgDark,
      appBar: AppBar(
        backgroundColor: VDRTheme.bgDark,
        elevation: 0,
        centerTitle: true,
        title: ShaderMask(
          shaderCallback: (b) => VDRTheme.mainGradient.createShader(b),
          child: const Text(
            "MY WISHLIST",
            style: TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.bold,
              letterSpacing: 2.0,
              fontSize: 18,
            ),
          ),
        ),
        actions: [
          if (wishlistItems.isNotEmpty)
            Padding(
              padding: const EdgeInsets.only(right: 12),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: VDRTheme.primary.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: VDRTheme.primary.withOpacity(0.4)),
                ),
                child: Text(
                  "${wishlistItems.length} items",
                  style: const TextStyle(
                    color: VDRTheme.primary,
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ),
        ],
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: VDRTheme.primary))
          : wishlistItems.isEmpty
              ? Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(24),
                        decoration: BoxDecoration(
                          color: VDRTheme.bgCard2,
                          shape: BoxShape.circle,
                          border: Border.all(color: VDRTheme.border),
                        ),
                        child: const Icon(Icons.favorite_border, size: 52, color: VDRTheme.textMuted),
                      ),
                      const SizedBox(height: 20),
                      const Text(
                        "Your Wishlist is Empty!",
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 17,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 8),
                      const Text(
                        "Save dresses you love to try them later.",
                        style: TextStyle(color: VDRTheme.textMuted, fontSize: 12),
                      ),
                    ],
                  ),
                )
              : ListView.builder(
                  padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 12),
                  itemCount: wishlistItems.length,
                  itemBuilder: (context, index) {
                    return _buildWishlistItem(context, wishlistItems[index]);
                  },
                ),
    );
  }

  Widget _buildWishlistItem(BuildContext context, dynamic rawItem) {
    final item = Product.fromJson(rawItem['product']);
    final int wishlistId = rawItem['id'];

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: VDRTheme.bgCard2,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: VDRTheme.border),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.3),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          // Product Image with glow
          Container(
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(14),
              boxShadow: [
                BoxShadow(
                  color: VDRTheme.primary.withOpacity(0.2),
                  blurRadius: 10,
                  offset: const Offset(0, 3),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(14),
              child: Image.network(
                item.imageUrl.startsWith('http')
                    ? item.imageUrl
                    : "${ApiService.baseUrl.replaceAll('/api', '')}${item.imageUrl}",
                height: 86,
                width: 86,
                fit: BoxFit.cover,
                errorBuilder: (_, __, ___) => Container(
                  height: 86,
                  width: 86,
                  color: VDRTheme.bgCard,
                  child: const Icon(Icons.broken_image, color: VDRTheme.textMuted),
                ),
              ),
            ),
          ),
          const SizedBox(width: 14),

          // Details
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
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
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                  decoration: BoxDecoration(
                    color: VDRTheme.bgCard,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: VDRTheme.border),
                  ),
                  child: Text(
                    item.category,
                    style: const TextStyle(color: VDRTheme.textSub, fontSize: 10),
                  ),
                ),
                const SizedBox(height: 8),
                ShaderMask(
                  shaderCallback: (b) => const LinearGradient(
                    colors: [VDRTheme.gradB, VDRTheme.gradC],
                  ).createShader(b),
                  child: Text(
                    "\$${item.price.toStringAsFixed(2)}",
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
          ),

          // Actions
          Column(
            children: [
              // Delete
              GestureDetector(
                onTap: () async {
                  // ── Confirmation Dialog ──────────────────────
                  final confirm = await showDialog<bool>(
                    context: context,
                    builder: (ctx) => AlertDialog(
                      backgroundColor: VDRTheme.bgCard2,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
                      title: const Row(
                        children: [
                          Icon(Icons.delete_outline_rounded, color: Colors.redAccent, size: 22),
                          SizedBox(width: 8),
                          Text("Remove Item?", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                        ],
                      ),
                      content: Text(
                        "Are you sure you want to remove '${item.name}' from your wishlist?",
                        style: const TextStyle(color: VDRTheme.textSub, height: 1.6),
                      ),
                      actions: [
                        TextButton(
                          onPressed: () => Navigator.pop(ctx, false),
                          child: const Text("Cancel", style: TextStyle(color: VDRTheme.textSub)),
                        ),
                        Container(
                          margin: const EdgeInsets.only(right: 8, bottom: 4),
                          decoration: BoxDecoration(
                            color: Colors.redAccent.withOpacity(0.12),
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: Colors.redAccent.withOpacity(0.4)),
                          ),
                          child: TextButton(
                            onPressed: () => Navigator.pop(ctx, true),
                            child: const Text("Remove", style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                          ),
                        ),
                      ],
                    ),
                  );
                  if (confirm == true) {
                    await ApiService.removeFromWishlist(wishlistId);
                    _loadWishlist();
                    if (context.mounted) {
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                        content: const Row(children: [
                          Icon(Icons.check_circle_outline, color: Colors.white, size: 16),
                          SizedBox(width: 8),
                          Text("Removed from wishlist.", style: TextStyle(color: Colors.white)),
                        ]),
                        backgroundColor: VDRTheme.borderGlow,
                        behavior: SnackBarBehavior.floating,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ));
                    }
                  }
                },
                child: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.redAccent.withOpacity(0.12),
                    shape: BoxShape.circle,
                    border: Border.all(color: Colors.redAccent.withOpacity(0.3)),
                  ),
                  child: const Icon(Icons.delete_outline, color: Colors.redAccent, size: 18),
                ),
              ),
              const SizedBox(height: 8),
              // Try On
              GestureDetector(
                onTap: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => ProductDetailScreen(product: item),
                    ),
                  );
                },
                child: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [VDRTheme.gradA, VDRTheme.gradB],
                    ),
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: VDRTheme.primary.withOpacity(0.35),
                        blurRadius: 10,
                        offset: const Offset(0, 3),
                      ),
                    ],
                  ),
                  child: const Icon(Icons.auto_awesome, color: Colors.white, size: 18),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}
