import 'package:flutter/material.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';
import '../login_register_screen.dart';
import 'add_product_screen.dart';
import '../user/user_dashboard_screen.dart' show VDRTheme;

// ─── Light Theme colors for Admin ────────────────────────────────
const Color _bg       = Color(0xFFFAF9F6);
const Color _white    = Color(0xFFFFFFFF);
const Color _gold     = Color(0xFFD4AF37);
const Color _charcoal = Color(0xFF2F2F2F);
const Color _gray     = Color(0xFF555555);
const Color _border   = Color(0xFFEAEAEA);

class AdminDashboardScreen extends StatefulWidget {
  const AdminDashboardScreen({super.key});

  @override
  State<AdminDashboardScreen> createState() => _AdminDashboardScreenState();
}

class _AdminDashboardScreenState extends State<AdminDashboardScreen> {
  List<Product> products = [];
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadAdminProducts();
  }

  Future<void> _loadAdminProducts() async {
    setState(() => isLoading = true);
    try {
      final list = await ApiService.fetchProducts();
      setState(() => products = list);
    } catch (e) {
      debugPrint("Error loading products: $e");
    } finally {
      setState(() => isLoading = false);
    }
  }

  Future<void> _editProduct(Product item) async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(builder: (_) => AddProductScreen(productToEdit: item)),
    );
    if (result == true) _loadAdminProducts();
  }

  Future<void> _confirmDeleteProduct(Product item) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: _white,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Delete Garment?", style: TextStyle(color: _charcoal, fontWeight: FontWeight.bold)),
        content: Text(
          "Are you sure you want to permanently delete '${item.name}' from the inventory?",
          style: const TextStyle(color: _gray, height: 1.6),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text("Cancel", style: TextStyle(color: _gray)),
          ),
          Container(
            margin: const EdgeInsets.only(right: 8, bottom: 4),
            decoration: BoxDecoration(
              color: Colors.redAccent.withOpacity(0.1),
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: Colors.redAccent.withOpacity(0.4)),
            ),
            child: TextButton(
              onPressed: () => Navigator.pop(ctx, true),
              child: const Text("Delete", style: TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
            ),
          ),
        ],
      ),
    );

    if (confirm == true) {
      setState(() => isLoading = true);
      try {
        final res = await ApiService.deleteProduct(item.id);
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Row(children: [
            Icon(res['status'] == 'success' ? Icons.check_circle : Icons.error_outline,
                color: Colors.white, size: 16),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                res['message'] ?? (res['status'] == 'success' ? 'Deleted!' : 'Failed.'),
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w500),
              ),
            ),
          ]),
          backgroundColor: res['status'] == 'success' ? _gold : Colors.redAccent,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ));
        if (res['status'] == 'success') _loadAdminProducts();
      } catch (e) {
        debugPrint("Delete error: $e");
      } finally {
        if (mounted) setState(() => isLoading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: _bg,
      appBar: AppBar(
        backgroundColor: _white,
        elevation: 0,
        shadowColor: Colors.black12,
        surfaceTintColor: Colors.transparent,
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(7),
              decoration: BoxDecoration(
                gradient: VDRTheme.mainGradient,
                borderRadius: BorderRadius.circular(10),
              ),
              child: const Icon(Icons.admin_panel_settings, color: Colors.white, size: 18),
            ),
            const SizedBox(width: 10),
            const Text(
              "ADMIN CONSOLE",
              style: TextStyle(
                color: _gold,
                fontWeight: FontWeight.w900,
                fontSize: 18,
                letterSpacing: 1.5,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh_rounded, color: _charcoal),
            onPressed: _loadAdminProducts,
            tooltip: "Refresh",
          ),
          Padding(
            padding: const EdgeInsets.only(right: 8),
            child: IconButton(
              icon: const Icon(Icons.logout_rounded, color: Colors.redAccent),
              onPressed: () => Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (_) => const LoginRegisterScreen()),
              ),
              tooltip: "Logout",
            ),
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: _border),
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final result = await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const AddProductScreen()),
          );
          if (result == true) _loadAdminProducts();
        },
        backgroundColor: _gold,
        elevation: 4,
        child: const Icon(Icons.add_rounded, color: Colors.white, size: 28),
      ),
      body: isLoading
          ? Center(child: CircularProgressIndicator(color: _gold))
          : RefreshIndicator(
              onRefresh: _loadAdminProducts,
              color: _gold,
              backgroundColor: _white,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(18),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // ── Stats Card ──────────────────────────────────
                    _buildTotalGarmentsCard(),
                    const SizedBox(height: 24),

                    // ── Section Title ────────────────────────────────
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          "Uploaded Garments",
                          style: TextStyle(color: _charcoal, fontSize: 17, fontWeight: FontWeight.bold),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                          decoration: BoxDecoration(
                            color: _gold.withOpacity(0.1),
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(color: _gold.withOpacity(0.3)),
                          ),
                          child: Text(
                            "${products.length} Items",
                            style: const TextStyle(color: _gold, fontSize: 11, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),

                    // ── Products Grid ─────────────────────────────────
                    products.isEmpty
                        ? Container(
                            height: 250,
                            alignment: Alignment.center,
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: const [
                                Icon(Icons.inventory_2_outlined, size: 52, color: Color(0xFFAAAAAA)),
                                SizedBox(height: 14),
                                Text(
                                  "No garments yet.\nTap + to upload your first dress!",
                                  textAlign: TextAlign.center,
                                  style: TextStyle(color: _gray, height: 1.6),
                                ),
                              ],
                            ),
                          )
                        : GridView.builder(
                            shrinkWrap: true,
                            physics: const NeverScrollableScrollPhysics(),
                            gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                              crossAxisCount: 2,
                              crossAxisSpacing: 14,
                              mainAxisSpacing: 14,
                              childAspectRatio: 0.72,
                            ),
                            itemCount: products.length,
                            itemBuilder: (context, index) => _buildGridItem(products[index]),
                          ),
                    const SizedBox(height: 90),
                  ],
                ),
              ),
            ),
    );
  }

  // ── Full-width Total Garments Banner ──────────────────────────────
  Widget _buildTotalGarmentsCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: _border),
        boxShadow: [
          BoxShadow(color: _gold.withOpacity(0.08), blurRadius: 20, offset: const Offset(0, 4)),
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 10, offset: const Offset(0, 2)),
        ],
      ),
      child: Row(
        children: [
          // Icon with gold gradient
          Container(
            width: 64,
            height: 64,
            decoration: BoxDecoration(
              gradient: VDRTheme.mainGradient,
              borderRadius: BorderRadius.circular(16),
              boxShadow: [
                BoxShadow(color: _gold.withOpacity(0.35), blurRadius: 14, offset: const Offset(0, 5)),
              ],
            ),
            child: const Icon(Icons.checkroom_rounded, color: Colors.white, size: 32),
          ),
          const SizedBox(width: 18),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  "TOTAL GARMENTS",
                  style: TextStyle(
                    color: _gray,
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1.5,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  products.length.toString(),
                  style: const TextStyle(
                    color: _charcoal,
                    fontSize: 40,
                    fontWeight: FontWeight.w900,
                    height: 1,
                  ),
                ),
                const SizedBox(height: 4),
                const Text(
                  "items in inventory",
                  style: TextStyle(color: Color(0xFFAAAAAA), fontSize: 12),
                ),
              ],
            ),
          ),
          Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: Colors.green.withOpacity(0.1),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.green.withOpacity(0.3)),
                ),
                child: Row(
                  children: const [
                    Icon(Icons.circle, color: Colors.green, size: 8),
                    SizedBox(width: 5),
                    Text("Live DB", style: TextStyle(color: Colors.green, fontSize: 11, fontWeight: FontWeight.bold)),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildGridItem(Product item) {
    return Container(
      decoration: BoxDecoration(
        color: _white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: _border),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.06), blurRadius: 10, offset: const Offset(0, 4)),
        ],
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Expanded(
              child: Stack(
                fit: StackFit.expand,
                children: [
                  Image.network(
                    item.imageUrl.startsWith('http')
                        ? item.imageUrl
                        : "${ApiService.baseUrl.replaceAll('/api', '')}${item.imageUrl}",
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      color: const Color(0xFFF5F5F5),
                      child: const Center(child: Icon(Icons.broken_image, color: Color(0xFFAAAAAA))),
                    ),
                  ),
                  // Price tag
                  Positioned(
                    bottom: 8, right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: _gold,
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        "\$${item.price.toStringAsFixed(2)}",
                        style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold),
                      ),
                    ),
                  ),
                  // Edit & Delete buttons
                  Positioned(
                    top: 8, left: 8,
                    child: Row(
                      children: [
                        _actionBtn(Icons.edit_rounded, _charcoal, Colors.white, () => _editProduct(item)),
                        const SizedBox(width: 6),
                        _actionBtn(Icons.delete_outline_rounded, Colors.redAccent, Colors.white, () => _confirmDeleteProduct(item)),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
              color: _white,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(item.name, maxLines: 1, overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: _charcoal, fontSize: 13, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 2),
                  Text(item.description, maxLines: 1, overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: _gray, fontSize: 10)),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _actionBtn(IconData icon, Color iconColor, Color bgColor, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(7),
        decoration: BoxDecoration(
          color: bgColor.withOpacity(0.9),
          shape: BoxShape.circle,
          boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.15), blurRadius: 6)],
        ),
        child: Icon(icon, size: 14, color: iconColor),
      ),
    );
  }
}
