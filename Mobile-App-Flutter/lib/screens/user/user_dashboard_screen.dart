import 'package:flutter/material.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';
import '../login_register_screen.dart';
import 'product_detail_screen.dart';
import 'cart_screen.dart';
import 'wishlist_screen.dart';
import '../../ai_tryon_screen.dart';

// ─── Theme Constants ──────────────────────────────────────────────
class VDRTheme {
  static const Color bgDark     = Color(0xFFFAF9F6);   // Soft Ivory
  static const Color bgCard     = Color(0xFFFFFFFF);   // White
  static const Color bgCard2    = Color(0xFFFFFFFF);   // White
  static const Color border     = Color(0xFFEAEAEA);   // Light Gray
  static const Color borderGlow = Color(0xFFD4AF37);   // Soft Gold
  static const Color primary    = Color(0xFFD4AF37);   // Soft Gold
  static const Color secondary  = Color(0xFF2F2F2F);   // Dark Charcoal
  static const Color gradA      = Color(0xFFD4AF37);   // Soft Gold
  static const Color gradB      = Color(0xFFE5C86C);   // Lighter Gold
  static const Color gradC      = Color(0xFFD4AF37);   // Soft Gold
  static const Color textSub    = Color(0xFF2F2F2F);   // Dark Charcoal
  static const Color textMuted  = Color(0xFF555555);   // Dark Gray
  static const Color bottomBar  = Color(0xFFFFFFFF);   // White

  static const LinearGradient mainGradient = LinearGradient(
    colors: [gradA, gradB, gradC],
  );
  static const LinearGradient cardGradient = LinearGradient(
    colors: [Color(0xFFFFFFFF), Color(0xFFFFFFFF)],
    begin: Alignment.topLeft,
    end: Alignment.bottomRight,
  );
}
// ─────────────────────────────────────────────────────────────────

class UserDashboardScreen extends StatefulWidget {
  const UserDashboardScreen({super.key});

  @override
  State<UserDashboardScreen> createState() => _UserDashboardScreenState();
}

class _UserDashboardScreenState extends State<UserDashboardScreen> {
  int _currentIndex = 0;

  final List<Widget> _screens = [
    const UserHomeTab(),
    const AITryOnScreen(),
    const WishlistScreen(),
    const CartScreen(),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: VDRTheme.bgDark,
      body: _screens[_currentIndex],
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          border: Border(
            top: BorderSide(color: VDRTheme.border.withOpacity(0.8), width: 1),
          ),
          boxShadow: [
            BoxShadow(
              color: VDRTheme.primary.withOpacity(0.08),
              blurRadius: 20,
              offset: const Offset(0, -5),
            ),
          ],
        ),
        child: BottomNavigationBar(
          currentIndex: _currentIndex,
          onTap: (index) => setState(() => _currentIndex = index),
          type: BottomNavigationBarType.fixed,
          backgroundColor: VDRTheme.bottomBar,
          selectedItemColor: VDRTheme.primary,
          unselectedItemColor: VDRTheme.textMuted,
          showSelectedLabels: true,
          showUnselectedLabels: true,
          selectedFontSize: 11,
          unselectedFontSize: 11,
          items: const [
            BottomNavigationBarItem(
              icon: Icon(Icons.home_outlined),
              activeIcon: Icon(Icons.home),
              label: "Studio",
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.auto_awesome_outlined),
              activeIcon: Icon(Icons.auto_awesome),
              label: "AI Try-On",
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.favorite_border),
              activeIcon: Icon(Icons.favorite),
              label: "Wishlist",
            ),
            BottomNavigationBarItem(
              icon: Icon(Icons.shopping_cart_outlined),
              activeIcon: Icon(Icons.shopping_cart),
              label: "Cart",
            ),
          ],
        ),
      ),
    );
  }
}

// ─── Home Tab ─────────────────────────────────────────────────────
class UserHomeTab extends StatefulWidget {
  const UserHomeTab({super.key});

  @override
  State<UserHomeTab> createState() => _UserHomeTabState();
}

class _UserHomeTabState extends State<UserHomeTab> {
  List<Product> products = [];
  bool isLoading = true;
  String activeCategory = "All";
  String searchQuery = "";

  final List<String> categories = [
    "All", "Dresses", "Hoodies", "T-Shirts", "Jackets", "Pants", "Traditional"
  ];

  @override
  void initState() {
    super.initState();
    _loadProducts();
  }

  Future<void> _loadProducts() async {
    setState(() => isLoading = true);
    try {
      final list = await ApiService.fetchProducts();
      setState(() => products = list);
    } catch (e) {
      debugPrint("Error fetching user products: $e");
    } finally {
      setState(() => isLoading = false);
    }
  }

  List<Product> get filteredProducts {
    List<Product> filtered = products;
    if (activeCategory != "All") {
      filtered = filtered
          .where((p) => p.category.toLowerCase() == activeCategory.toLowerCase())
          .toList();
    }
    if (searchQuery.isNotEmpty) {
      filtered = filtered
          .where((p) => p.name.toLowerCase().contains(searchQuery.toLowerCase()))
          .toList();
    }
    return filtered;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: VDRTheme.bgDark,
      appBar: AppBar(
        backgroundColor: VDRTheme.bgDark,
        elevation: 0,
        title: Row(
          children: [
            // Logo image
            Container(
              width: 36,
              height: 36,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(10),
                boxShadow: [
                  BoxShadow(
                    color: VDRTheme.primary.withOpacity(0.4),
                    blurRadius: 10,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(10),
                child: Image.asset('assets/logo.png', fit: BoxFit.cover),
              ),
            ),
            const SizedBox(width: 10),
            ShaderMask(
              shaderCallback: (bounds) => VDRTheme.mainGradient.createShader(bounds),
              child: const Text(
                "VIRTUAL DRESS ROOM",
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w900,
                  color: Colors.white,
                  letterSpacing: 1.2,
                ),
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout, color: VDRTheme.textMuted),
            onPressed: () {
              Navigator.pushReplacement(
                context,
                MaterialPageRoute(builder: (_) => const LoginRegisterScreen()),
              );
            },
          ),
        ],
      ),
      body: isLoading
          ? Center(
              child: CircularProgressIndicator(color: VDRTheme.primary),
            )
          : RefreshIndicator(
              onRefresh: _loadProducts,
              color: VDRTheme.primary,
              backgroundColor: VDRTheme.bgCard2,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                padding: const EdgeInsets.symmetric(horizontal: 18.0, vertical: 12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    _buildPromoBanner(),
                    const SizedBox(height: 20),
                    _buildSearchBar(),
                    const SizedBox(height: 20),
                    _buildCategories(),
                    const SizedBox(height: 20),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          "Exclusive Garments",
                          style: TextStyle(
                            color: Color(0xFF2F2F2F),
                            fontSize: 17,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        Text(
                          "${filteredProducts.length} items",
                          style: const TextStyle(color: VDRTheme.textMuted, fontSize: 12),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    filteredProducts.isEmpty
                        ? Container(
                            height: 200,
                            alignment: Alignment.center,
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: const [
                                Icon(Icons.search_off, color: VDRTheme.textMuted, size: 48),
                                SizedBox(height: 12),
                                Text(
                                  "No dresses found.",
                                  style: TextStyle(color: VDRTheme.textSub),
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
                              childAspectRatio: 0.68,
                            ),
                            itemCount: filteredProducts.length,
                            itemBuilder: (context, index) {
                              return _buildProductCard(filteredProducts[index]);
                            },
                          ),
                    const SizedBox(height: 20),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildPromoBanner() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        gradient: const LinearGradient(
          colors: [Color(0xFF3A0070), Color(0xFF8B00CC), Color(0xFFD500F9)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: VDRTheme.primary.withOpacity(0.4),
            blurRadius: 24,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: const Text(
                    "✨ AI POWERED",
                    style: TextStyle(color: Colors.white, fontSize: 10, fontWeight: FontWeight.bold, letterSpacing: 1),
                  ),
                ),
                const SizedBox(height: 10),
                const Text(
                  "Virtual\nDress Studio",
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 22,
                    fontWeight: FontWeight.w900,
                    height: 1.2,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  "Try on any dress virtually with AI magic!",
                  style: TextStyle(color: Colors.white.withOpacity(0.8), fontSize: 11, height: 1.4),
                ),
                const SizedBox(height: 14),
                ElevatedButton(
                  onPressed: () {
                    showDialog(
                      context: context,
                      builder: (ctx) => AlertDialog(
                        backgroundColor: VDRTheme.bgCard2,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                        title: const Text("How AI Try-On Works", style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold)),
                        content: const Text(
                          "1. Browse & select a dress\n2. Tap 'Try on with AI'\n3. Upload your photo\n4. Watch the magic happen! ✨",
                          style: TextStyle(color: VDRTheme.textSub, height: 1.8),
                        ),
                        actions: [
                          TextButton(
                            onPressed: () => Navigator.pop(ctx),
                            child: const Text("Got It!", style: TextStyle(color: VDRTheme.primary, fontWeight: FontWeight.bold)),
                          ),
                        ],
                      ),
                    );
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.white,
                    foregroundColor: VDRTheme.gradA,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 10),
                    elevation: 0,
                  ),
                  child: const Text(
                    "How it works",
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.bold),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 10),
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: const Icon(Icons.auto_awesome_motion, size: 52, color: Colors.white70),
          ),
        ],
      ),
    );
  }

  Widget _buildSearchBar() {
    return Container(
      decoration: BoxDecoration(
        color: VDRTheme.bgCard2,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: VDRTheme.border),
        boxShadow: [
          BoxShadow(
            color: VDRTheme.primary.withOpacity(0.06),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: TextField(
        onChanged: (value) => setState(() => searchQuery = value),
        style: const TextStyle(color: Color(0xFF2F2F2F), fontSize: 14),
        decoration: InputDecoration(
          hintText: "Search dresses, hoodies, jackets...",
          hintStyle: const TextStyle(color: VDRTheme.textMuted, fontSize: 13),
          prefixIcon: const Icon(Icons.search_rounded, color: VDRTheme.textSub),
          suffixIcon: searchQuery.isNotEmpty
              ? IconButton(
                  icon: const Icon(Icons.close, color: VDRTheme.textMuted, size: 18),
                  onPressed: () => setState(() => searchQuery = ""),
                )
              : null,
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(vertical: 15, horizontal: 4),
        ),
      ),
    );
  }

  Widget _buildCategories() {
    return SizedBox(
      height: 38,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: categories.length,
        itemBuilder: (context, index) {
          final cat = categories[index];
          final isSelected = activeCategory == cat;
          return Padding(
            padding: const EdgeInsets.only(right: 8),
            child: GestureDetector(
              onTap: () => setState(() => activeCategory = cat),
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 200),
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                decoration: BoxDecoration(
                  gradient: isSelected
                      ? const LinearGradient(colors: [VDRTheme.gradA, VDRTheme.gradB])
                      : null,
                  color: isSelected ? null : VDRTheme.bgCard2,
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(
                    color: isSelected ? Colors.transparent : VDRTheme.border,
                  ),
                  boxShadow: isSelected
                      ? [BoxShadow(color: VDRTheme.primary.withOpacity(0.35), blurRadius: 10, offset: const Offset(0, 3))]
                      : [],
                ),
                child: Text(
                  cat,
                  style: TextStyle(
                    color: isSelected ? Colors.white : VDRTheme.textSub,
                    fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                    fontSize: 12,
                  ),
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildProductCard(Product product) {
    return GestureDetector(
      onTap: () => Navigator.push(
        context,
        MaterialPageRoute(builder: (_) => ProductDetailScreen(product: product)),
      ),
      child: Container(
        decoration: BoxDecoration(
          color: VDRTheme.bgCard,
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: VDRTheme.border),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.4),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
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
                      product.imageUrl.startsWith('http')
                          ? product.imageUrl
                          : "${ApiService.baseUrl.replaceAll('/api', '')}${product.imageUrl}",
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => Container(
                        color: VDRTheme.bgCard2,
                        child: const Center(child: Icon(Icons.broken_image, color: VDRTheme.textMuted)),
                      ),
                    ),
                    // Gradient overlay bottom
                    Positioned(
                      bottom: 0, left: 0, right: 0,
                      child: Container(
                        height: 40,
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [Colors.transparent, Colors.black.withOpacity(0.5)],
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                          ),
                        ),
                      ),
                    ),
                    // Favourite badge
                    Positioned(
                      top: 8, right: 8,
                      child: Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: VDRTheme.bgDark.withOpacity(0.75),
                          shape: BoxShape.circle,
                          border: Border.all(color: VDRTheme.border),
                        ),
                        child: const Icon(Icons.favorite_border, color: Color(0xFFE040FB), size: 14),
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.all(10),
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [VDRTheme.bgCard, VDRTheme.bgCard2],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      product.name,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(color: Color(0xFF2F2F2F), fontSize: 13, fontWeight: FontWeight.bold),
                    ),
                    const SizedBox(height: 6),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        ShaderMask(
                          shaderCallback: (b) => const LinearGradient(
                            colors: [VDRTheme.gradB, VDRTheme.gradC],
                          ).createShader(b),
                          child: Text(
                            "\$${product.price.toStringAsFixed(2)}",
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                          decoration: BoxDecoration(
                            color: VDRTheme.primary.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(color: VDRTheme.primary.withOpacity(0.3)),
                          ),
                          child: const Text(
                            "AI Try",
                            style: TextStyle(color: VDRTheme.primary, fontSize: 9, fontWeight: FontWeight.bold),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
