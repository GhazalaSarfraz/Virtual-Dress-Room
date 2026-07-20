import 'package:flutter/material.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';
import '../../ai_tryon_screen.dart';
import 'user_dashboard_screen.dart' show VDRTheme;

class ProductDetailScreen extends StatelessWidget {
  final Product product;
  const ProductDetailScreen({super.key, required this.product});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: VDRTheme.bgDark,
      body: Stack(
        children: [
          CustomScrollView(
            slivers: [
              // ── Hero Image AppBar ───────────────────────────────────
              SliverAppBar(
                expandedHeight: 440,
                pinned: true,
                backgroundColor: VDRTheme.bgDark,
                leading: Padding(
                  padding: const EdgeInsets.all(8),
                  child: Container(
                    decoration: BoxDecoration(
                      color: Colors.black.withOpacity(0.5),
                      shape: BoxShape.circle,
                      border: Border.all(color: VDRTheme.border),
                    ),
                    child: IconButton(
                      icon: const Icon(Icons.arrow_back_ios_new, color: Colors.white, size: 16),
                      onPressed: () => Navigator.pop(context),
                    ),
                  ),
                ),
                actions: [
                  Padding(
                    padding: const EdgeInsets.only(right: 12, top: 8, bottom: 8),
                    child: Container(
                      decoration: BoxDecoration(
                        color: Colors.black.withOpacity(0.5),
                        shape: BoxShape.circle,
                        border: Border.all(color: VDRTheme.borderGlow.withOpacity(0.5)),
                        boxShadow: [
                          BoxShadow(color: VDRTheme.primary.withOpacity(0.3), blurRadius: 10),
                        ],
                      ),
                      child: IconButton(
                        icon: const Icon(Icons.favorite_border_rounded, color: Color(0xFFE040FB)),
                        onPressed: () async {
                           final res = await ApiService.addToWishlist(product.id);
                           if (!context.mounted) return;
                           ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                             content: Row(children: [
                               Icon(
                                 res['status'] == 'success' ? Icons.favorite : Icons.error_outline,
                                 color: Colors.white, size: 16,
                               ),
                               const SizedBox(width: 8),
                               Expanded(
                                 child: Text(
                                   res['message'] ?? "Done",
                                   style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w500),
                                 ),
                               ),
                             ]),
                             backgroundColor: res['status'] == 'success'
                                 ? VDRTheme.borderGlow
                                 : Colors.redAccent,
                             behavior: SnackBarBehavior.floating,
                             shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                           ));
                         },
                      ),
                    ),
                  ),
                ],
                flexibleSpace: FlexibleSpaceBar(
                  background: Stack(
                    fit: StackFit.expand,
                    children: [
                      Image.network(
                        product.imageUrl.startsWith('http')
                            ? product.imageUrl
                            : "${ApiService.baseUrl.replaceAll('/api', '')}${product.imageUrl}",
                        fit: BoxFit.cover,
                        errorBuilder: (_, __, ___) => Container(
                          color: VDRTheme.bgCard2,
                          child: const Center(
                            child: Icon(Icons.broken_image, color: VDRTheme.textMuted, size: 80),
                          ),
                        ),
                      ),
                      // Bottom fade to dark
                      Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [
                              Colors.transparent,
                              Colors.transparent,
                              VDRTheme.bgDark.withOpacity(0.8),
                              VDRTheme.bgDark,
                            ],
                            stops: const [0.0, 0.5, 0.85, 1.0],
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                          ),
                        ),
                      ),
                      // Top fade for status bar readability
                      Container(
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [Colors.black.withOpacity(0.4), Colors.transparent],
                            begin: Alignment.topCenter,
                            end: Alignment.center,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // ── Product Info ────────────────────────────────────────
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(22, 4, 22, 160),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Category badge
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(colors: [VDRTheme.gradA, VDRTheme.gradB]),
                          borderRadius: BorderRadius.circular(20),
                          boxShadow: [
                            BoxShadow(color: VDRTheme.primary.withOpacity(0.4), blurRadius: 10),
                          ],
                        ),
                        child: Text(
                          product.category.toUpperCase(),
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1.5,
                          ),
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Name & Price row
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(
                            child: Text(
                              product.name,
                              style: const TextStyle(
                                color: Color(0xFF2F2F2F),
                                fontSize: 26,
                                fontWeight: FontWeight.bold,
                                height: 1.2,
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          ShaderMask(
                            shaderCallback: (b) => const LinearGradient(
                              colors: [VDRTheme.gradB, VDRTheme.gradC],
                            ).createShader(b),
                            child: Text(
                              "\$${product.price.toStringAsFixed(2)}",
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 26,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // Rating & VTON badge row
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                            decoration: BoxDecoration(
                              color: VDRTheme.bgCard2,
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: VDRTheme.border),
                            ),
                            child: const Row(
                              children: [
                                Icon(Icons.star_rounded, color: Colors.amber, size: 16),
                                SizedBox(width: 4),
                                Text("4.9", style: TextStyle(color: Color(0xFF2F2F2F), fontWeight: FontWeight.bold, fontSize: 13)),
                                SizedBox(width: 4),
                                Text("(120)", style: TextStyle(color: VDRTheme.textSub, fontSize: 11)),
                              ],
                            ),
                          ),
                          const SizedBox(width: 10),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                            decoration: BoxDecoration(
                              color: VDRTheme.primary.withOpacity(0.1),
                              borderRadius: BorderRadius.circular(10),
                              border: Border.all(color: VDRTheme.primary.withOpacity(0.35)),
                            ),
                            child: const Row(
                              children: [
                                Icon(Icons.auto_awesome, color: VDRTheme.primary, size: 14),
                                SizedBox(width: 5),
                                Text(
                                  "AI Try-On Ready",
                                  style: TextStyle(color: VDRTheme.primary, fontSize: 11, fontWeight: FontWeight.bold),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 28),

                      // Divider
                      Container(height: 1, color: VDRTheme.border),
                      const SizedBox(height: 24),

                      // Description title
                      const Text(
                        "About this Garment",
                        style: TextStyle(color: Color(0xFF2F2F2F), fontSize: 16, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        product.description,
                        style: const TextStyle(
                          color: Color(0xFF555555),
                          fontSize: 14,
                          height: 1.7,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ],
          ),

          // ── Bottom Action Bar ───────────────────────────────────────
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
                    color: VDRTheme.primary.withOpacity(0.12),
                    blurRadius: 30,
                    offset: const Offset(0, -8),
                  ),
                ],
              ),
              child: Row(
                children: [
                  // Add to Cart button
                  GestureDetector(
                    onTap: () async {
                      final res = await ApiService.addToCart(product.id);
                      if (!context.mounted) return;
                      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                        content: Row(children: [
                          Icon(
                            res['status'] == 'success' ? Icons.check_circle : Icons.error_outline,
                            color: Colors.white, size: 16,
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              res['message'] ?? "Done",
                              style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w500),
                            ),
                          ),
                        ]),
                        backgroundColor: res['status'] == 'success'
                            ? VDRTheme.borderGlow
                            : Colors.redAccent,
                        behavior: SnackBarBehavior.floating,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      ));
                    },
                    child: Container(
                      height: 54,
                      width: 54,
                      decoration: BoxDecoration(
                        color: VDRTheme.bgCard2,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: VDRTheme.borderGlow.withOpacity(0.4)),
                      ),
                      child: const Icon(Icons.shopping_cart_outlined, color: VDRTheme.primary),
                    ),
                  ),
                  const SizedBox(width: 14),

                  // Try On with AI button
                  Expanded(
                    child: Container(
                      height: 54,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(16),
                        gradient: VDRTheme.mainGradient,
                        boxShadow: [
                          BoxShadow(
                            color: VDRTheme.primary.withOpacity(0.55),
                            blurRadius: 22,
                            offset: const Offset(0, 7),
                          ),
                        ],
                      ),
                      child: ElevatedButton(
                        onPressed: () {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => AITryOnScreen(preselectedProduct: product),
                            ),
                          );
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.transparent,
                          shadowColor: Colors.transparent,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        ),
                        child: const Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.auto_awesome, color: Colors.white, size: 18),
                            SizedBox(width: 8),
                            Text(
                              "TRY ON WITH AI ✨",
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 14,
                                fontWeight: FontWeight.bold,
                                letterSpacing: 0.8,
                              ),
                            ),
                          ],
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
}
