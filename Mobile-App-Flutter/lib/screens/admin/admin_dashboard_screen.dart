import 'package:flutter/material.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';
import '../login_register_screen.dart';
import 'add_product_screen.dart';

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
    setState(() {
      isLoading = true;
    });
    try {
      final list = await ApiService.fetchProducts();
      setState(() {
        products = list;
      });
    } catch (e) {
      print("Error loading products: $e");
    } finally {
      setState(() {
        isLoading = false;
      });
    }
  }

  Future<void> _editProduct(Product item) async {
    final result = await Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => AddProductScreen(productToEdit: item),
      ),
    );
    if (result == true) {
      _loadAdminProducts(); // reload list
    }
  }

  Future<void> _confirmDeleteProduct(Product item) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        backgroundColor: const Color(0xFF1E1B38),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text("Delete Garment?", style: TextStyle(color: Colors.white)),
        content: Text(
          "Are you sure you want to permanently delete '${item.name}' from the inventory?",
          style: const TextStyle(color: Color(0xFF8B87B5), height: 1.5),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text("Cancel", style: TextStyle(color: Colors.grey)),
          ),
          TextButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text("Delete", style: TextStyle(color: Colors.redAccent)),
          ),
        ],
      ),
    );

    if (confirm == true) {
      setState(() {
        isLoading = true;
      });
      try {
        final res = await ApiService.deleteProduct(item.id);
        if (res['status'] == 'success') {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(res['message'] ?? 'Garment deleted successfully!'),
              backgroundColor: Colors.green,
            ),
          );
          _loadAdminProducts();
        } else {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(res['message'] ?? 'Failed to delete garment.'),
              backgroundColor: Colors.redAccent,
            ),
          );
        }
      } catch (e) {
        print("Delete error: $e");
      } finally {
        if (mounted) {
          setState(() {
            isLoading = false;
          });
        }
      }
    }
  }

  void _logout() {
    Navigator.pushReplacement(
      context,
      MaterialPageRoute(builder: (_) => const LoginRegisterScreen()),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F0C20),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1E1B38),
        title: const Text(
          "ADMIN CONSOLE",
          style: TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w900,
            fontSize: 20,
            letterSpacing: 1.5,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.white),
            onPressed: _loadAdminProducts,
          ),
          IconButton(
            icon: const Icon(Icons.logout, color: Colors.redAccent),
            onPressed: _logout,
          ),
        ],
        elevation: 0,
      ),
      floatingActionButton: Container(
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: const LinearGradient(
            colors: [Color(0xFF8A2387), Color(0xFFE94057), Color(0xFFF27121)],
          ),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFFE94057).withOpacity(0.4),
              blurRadius: 12,
              offset: const Offset(0, 4),
            )
          ],
        ),
        child: FloatingActionButton(
          onPressed: () async {
            final result = await Navigator.push(
              context,
              MaterialPageRoute(builder: (_) => const AddProductScreen()),
            );
            if (result == true) {
              _loadAdminProducts(); // reload list
            }
          },
          backgroundColor: Colors.transparent,
          elevation: 0,
          child: const Icon(Icons.add, color: Colors.white, size: 28),
        ),
      ),
      body: isLoading
          ? const Center(child: CircularProgressIndicator(color: Color(0xFFE94057)))
          : RefreshIndicator(
              onRefresh: _loadAdminProducts,
              color: const Color(0xFFE94057),
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.all(20.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    // Stats Row
                    Row(
                      children: [
                        Expanded(
                          child: _buildStatCard(
                            "Total Dresses",
                            products.length.toString(),
                            Icons.checkroom,
                            const Color(0xFF8A2387),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: _buildStatCard(
                            "Server VTON",
                            "Online",
                            Icons.cloud_done,
                            const Color(0xFF00B4D8),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),

                    // Title
                    const Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Text(
                          "Uploaded Garments",
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        Text(
                          "Live Database",
                          style: TextStyle(color: Color(0xFF8B87B5), fontSize: 12),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),

                    // Grid of dresses
                    if (products.isEmpty)
                      Container(
                        height: 250,
                        alignment: Alignment.center,
                        child: const Text(
                          "No garments uploaded yet.\nTap the + button to add.",
                          textAlign: TextAlign.center,
                          style: TextStyle(color: Color(0xFF8B87B5), height: 1.5),
                        ),
                      )
                    else
                      GridView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          crossAxisSpacing: 14,
                          mainAxisSpacing: 14,
                          childAspectRatio: 0.72,
                        ),
                        itemCount: products.length,
                        itemBuilder: (context, index) {
                          final item = products[index];
                          return _buildGridItem(item);
                        },
                      ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStatCard(String title, String val, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 16),
      decoration: BoxDecoration(
        color: const Color(0xFF1E1B38),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFF322E54)),
      ),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: color.withOpacity(0.15),
            radius: 20,
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(color: Color(0xFF8B87B5), fontSize: 11),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  val,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildGridItem(Product item) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFF1E1B38),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFF322E54)),
      ),
      child: ClipRRect(
        borderRadius: BorderRadius.circular(16),
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
                        : "http://10.222.89.186:8000${item.imageUrl}",
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => const Center(
                      child: Icon(Icons.broken_image, color: Colors.grey),
                    ),
                  ),
                  // Price Tag Overlay
                  Positioned(
                    top: 8,
                    right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: Colors.black.withOpacity(0.65),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        "\$${item.price.toStringAsFixed(2)}",
                        style: const TextStyle(
                          color: Color(0xFFF27121),
                          fontSize: 11,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                  // Edit & Delete Floating Overlays
                  Positioned(
                    top: 8,
                    left: 8,
                    child: Row(
                      children: [
                        CircleAvatar(
                          backgroundColor: Colors.black.withOpacity(0.7),
                          radius: 15,
                          child: IconButton(
                            icon: const Icon(Icons.edit, size: 12, color: Colors.white),
                            padding: EdgeInsets.zero,
                            onPressed: () => _editProduct(item),
                          ),
                        ),
                        const SizedBox(width: 6),
                        CircleAvatar(
                          backgroundColor: Colors.black.withOpacity(0.7),
                          radius: 15,
                          child: IconButton(
                            icon: const Icon(Icons.delete_outline, size: 12, color: Colors.redAccent),
                            padding: EdgeInsets.zero,
                            onPressed: () => _confirmDeleteProduct(item),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(10.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    item.name,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    item.description,
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(
                      color: Color(0xFF8B87B5),
                      fontSize: 10,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
