import 'dart:async';
import 'dart:convert';
import 'dart:typed_data';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:http/http.dart' as http;
import 'package:flutter_image_compress/flutter_image_compress.dart';
import 'models/product.dart';
import 'services/api_service.dart';

class AITryOnScreen extends StatefulWidget {
  final Product? preselectedProduct;
  const AITryOnScreen({super.key, this.preselectedProduct});

  @override
  State<AITryOnScreen> createState() => _AITryOnScreenState();
}

class _AITryOnScreenState extends State<AITryOnScreen> {
  Uint8List? humanImage;
  Uint8List? customClothImage;
  
  Product? selectedProduct;
  List<Product> availableProducts = [];
  
  String? resultImage;
  bool isLoading = false;
  bool isProductsLoading = false;
  String loadingProgressText = "Connecting to server...";

  final picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    if (widget.preselectedProduct != null) {
      selectedProduct = widget.preselectedProduct;
    }
    _loadCatalog();
  }

  Future<void> _loadCatalog() async {
    setState(() {
      isProductsLoading = true;
    });
    try {
      final list = await ApiService.fetchProducts();
      setState(() {
        availableProducts = list;
        // If no product is preselected, choose the first item as default
        if (selectedProduct == null && availableProducts.isNotEmpty) {
          selectedProduct = availableProducts.first;
        }
      });
    } catch (e) {
      print("Error loading tryon catalog: $e");
    } finally {
      setState(() {
        isProductsLoading = false;
      });
    }
  }

  // Compress Image to reduce network latency
  Future<Uint8List> compressImage(Uint8List imageBytes) async {
    final result = await FlutterImageCompress.compressWithList(
      imageBytes,
      minWidth: 768,
      minHeight: 1024,
      quality: 85,
    );
    return Uint8List.fromList(result);
  }

  // Pick Human Image
  Future pickHumanImage() async {
    final XFile? image = await picker.pickImage(
      source: ImageSource.gallery,
    );

    if (image != null) {
      final bytes = await image.readAsBytes();
      final compressed = await compressImage(bytes);
      setState(() {
        humanImage = compressed;
        resultImage = null; // Reset result when new image is uploaded
      });
    }
  }

  // Pick Custom Garment Image (Optional fallback)
  Future pickCustomClothImage() async {
    final XFile? image = await picker.pickImage(
      source: ImageSource.gallery,
    );

    if (image != null) {
      final bytes = await image.readAsBytes();
      final compressed = await compressImage(bytes);
      setState(() {
        customClothImage = compressed;
        selectedProduct = null; // Deselect catalog product when custom is chosen
        resultImage = null;
      });
    }
  }

  // Generate Virtual Try-On via Laravel API
  Future generateTryOn() async {
    if (humanImage == null) {
      showErrorSnackBar("Please upload your human photo first!");
      return;
    }

    if (selectedProduct == null && customClothImage == null) {
      showErrorSnackBar("Please select a dress from the catalog or upload a custom dress!");
      return;
    }

    setState(() {
      isLoading = true;
      loadingProgressText = "Processing images...";
    });

    // Start a dynamic timer to update progress texts and keep user engaged
    Timer? loadingTimer;
    int counter = 0;
    loadingTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
      if (!mounted || !isLoading) {
        timer.cancel();
        return;
      }
      counter++;
      setState(() {
        if (counter == 1) {
          loadingProgressText = "Uploading assets to Laravel...";
        } else if (counter == 2) {
          loadingProgressText = "Triggering Python VTON Model...";
        } else if (counter == 3) {
          loadingProgressText = "Generating outfit mapping (takes 15-25s)...";
        } else if (counter == 5) {
          loadingProgressText = "Refining image details...";
        } else if (counter == 7) {
          loadingProgressText = "Finalizing high-definition rendering...";
        }
      });
    });

    try {
      String humanBase64 = base64Encode(humanImage!);
      String clothUrl = "";

      if (selectedProduct != null) {
        clothUrl = selectedProduct!.imageUrl;
      } else {
        // Fallback: If custom local image is uploaded, we simulate by encoding it or fallback URL
        clothUrl = "https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=500&q=80";
      }

      // Call Laravel Try-On Endpoint
      final response = await ApiService.generateTryOn(
        humanImageBase64: humanBase64,
        clothImageUrl: clothUrl,
        description: selectedProduct?.name ?? "Beautiful custom garment",
      );

      loadingTimer.cancel();

      if (response["status"] == "success") {
        setState(() {
          resultImage = response["image"];
          isLoading = false;
        });
        showSuccessSnackBar("Virtual Try-On generated successfully!");
      } else {
        setState(() {
          isLoading = false;
        });
        showErrorSnackBar("Try-On Error: ${response["message"]}");
      }
    } catch (e) {
      loadingTimer.cancel();
      setState(() {
        isLoading = false;
      });
      showErrorSnackBar("Failed to connect to backend: $e");
    }
  }

  void showErrorSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(color: Colors.white)),
        backgroundColor: Colors.redAccent,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  void showSuccessSnackBar(String message) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message, style: const TextStyle(color: Colors.white)),
        backgroundColor: Colors.green,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F0C20),
      appBar: widget.preselectedProduct != null
          ? AppBar(
              backgroundColor: const Color(0xFF1E1B38),
              iconTheme: const IconThemeData(color: Colors.white),
              title: const Text("AI TRY-ON STUDIO", style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
              elevation: 0,
            )
          : null,
      body: Container(
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF0F0C20), Color(0xFF15102A)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Header (Only show if not pushed from detail page)
                if (widget.preselectedProduct == null) ...[
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      ShaderMask(
                        shaderCallback: (bounds) => const LinearGradient(
                          colors: [Color(0xFF8A2387), Color(0xFFE94057), Color(0xFFF27121)],
                        ).createShader(bounds),
                        child: const Text(
                          "AI TRY-ON STUDIO",
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.w900,
                            color: Colors.white,
                            letterSpacing: 2.0,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  const Center(
                    child: Text(
                      "Select a catalog garment and see how it fits you!",
                      style: TextStyle(color: Color(0xFF8B87B5), fontSize: 13),
                    ),
                  ),
                  const SizedBox(height: 20),
                ],

                // 1. Dynamic Catalog Dress Carousel
                const Text(
                  "Choose a Dress to Try On",
                  style: TextStyle(color: Colors.white, fontSize: 14, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 10),
                _buildCatalogCarousel(),
                const SizedBox(height: 24),

                // 2. Upload Workspace (Duo Layout: Selected Dress vs Human Photo)
                Row(
                  children: [
                    // Active Dress Preview
                    Expanded(
                      child: _buildActiveDressCard(),
                    ),
                    const SizedBox(width: 16),
                    // Human Image Picker
                    Expanded(
                      child: _buildHumanImageCard(),
                    ),
                  ],
                ),
                const SizedBox(height: 28),

                // 3. Action Trigger Button
                _buildGenerateButton(),
                const SizedBox(height: 36),

                // 4. Try-On Result Canvas
                if (resultImage != null) _buildResultCard() else _buildEmptyResultCard(),
                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildCatalogCarousel() {
    if (isProductsLoading) {
      return const SizedBox(
        height: 110,
        child: Center(child: CircularProgressIndicator(color: Color(0xFFE94057))),
      );
    }

    if (availableProducts.isEmpty) {
      return Container(
        height: 110,
        decoration: BoxDecoration(
          color: const Color(0xFF1E1B38),
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFF322E54)),
        ),
        alignment: Alignment.center,
        child: const Text("No clothes found in database", style: TextStyle(color: Color(0xFF8B87B5))),
      );
    }

    return SizedBox(
      height: 120,
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        itemCount: availableProducts.length,
        itemBuilder: (context, index) {
          final prod = availableProducts[index];
          final isSelected = selectedProduct?.id == prod.id;

          return GestureDetector(
            onTap: () {
              setState(() {
                selectedProduct = prod;
                customClothImage = null; // Clear custom upload
                resultImage = null;
              });
            },
            child: Container(
              width: 90,
              margin: const EdgeInsets.only(right: 12),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(14),
                border: Border.all(
                  color: isSelected ? const Color(0xFFE94057) : const Color(0xFF322E54),
                  width: isSelected ? 2 : 1,
                ),
              ),
              child: ClipRRect(
                borderRadius: BorderRadius.circular(12),
                child: Stack(
                  fit: StackFit.expand,
                  children: [
                    Image.network(
                      prod.imageUrl.startsWith('http') ? prod.imageUrl : "http://10.222.89.186:8000${prod.imageUrl}",
                      fit: BoxFit.cover
                    ),
                    if (isSelected)
                      Container(
                        color: const Color(0xFFE94057).withOpacity(0.2),
                        child: const Center(
                          child: Icon(Icons.check_circle, color: Colors.white, size: 24),
                        ),
                      ),
                  ],
                ),
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildActiveDressCard() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 4, bottom: 8),
          child: Text(
            "Selected Dress",
            style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold),
          ),
        ),
        Container(
          height: 190,
          decoration: BoxDecoration(
            color: const Color(0xFF1E1B38),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(
              color: selectedProduct != null ? const Color(0xFFE94057) : const Color(0xFF322E54),
              width: selectedProduct != null ? 1.5 : 1,
            ),
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(16),
            child: selectedProduct != null
                ? Image.network(
                    selectedProduct!.imageUrl.startsWith('http') ? selectedProduct!.imageUrl : "${ApiService.baseUrl.replaceAll('/api', '')}${selectedProduct!.imageUrl}",
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => const Center(
                      child: Icon(Icons.broken_image, color: Colors.grey),
                    ),
                  )
                : customClothImage != null
                    ? Image.memory(customClothImage!, fit: BoxFit.cover)
                    : Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          IconButton(
                            icon: const Icon(Icons.checkroom_outlined, size: 36, color: Color(0xFF8B87B5)),
                            onPressed: pickCustomClothImage,
                          ),
                          const SizedBox(height: 8),
                          const Text("No Dress", style: TextStyle(color: Color(0xFF8B87B5), fontSize: 12)),
                          const SizedBox(height: 4),
                          TextButton(
                            onPressed: pickCustomClothImage,
                            child: const Text("Upload Custom", style: TextStyle(color: Color(0xFFE94057), fontSize: 10)),
                          ),
                        ],
                      ),
          ),
        ),
      ],
    );
  }

  Widget _buildHumanImageCard() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Padding(
          padding: EdgeInsets.only(left: 4, bottom: 8),
          child: Text(
            "Your Photo",
            style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.bold),
          ),
        ),
        InkWell(
          onTap: isLoading ? null : pickHumanImage,
          borderRadius: BorderRadius.circular(16),
          child: Container(
            height: 190,
            decoration: BoxDecoration(
              color: const Color(0xFF1E1B38),
              borderRadius: BorderRadius.circular(16),
              border: Border.all(
                color: humanImage != null ? const Color(0xFFE94057) : const Color(0xFF322E54),
                width: humanImage != null ? 1.5 : 1,
              ),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: humanImage != null
                  ? Image.memory(humanImage!, fit: BoxFit.cover)
                  : const Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.add_a_photo_outlined, size: 36, color: Color(0xFF8B87B5)),
                        SizedBox(height: 12),
                        Text(
                          "Select Photo",
                          style: TextStyle(color: Color(0xFF8B87B5), fontSize: 12, fontWeight: FontWeight.bold),
                        ),
                        SizedBox(height: 4),
                        Text("Selfie / Portrait", style: TextStyle(color: Color(0xFF5B5785), fontSize: 10)),
                      ],
                    ),
            ),
          ),
        ),
      ],
    );
  }

  Widget _buildGenerateButton() {
    return Container(
      height: 56,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(16),
        gradient: isLoading
            ? null
            : const LinearGradient(
                colors: [Color(0xFF8A2387), Color(0xFFE94057), Color(0xFFF27121)],
              ),
        boxShadow: isLoading
            ? []
            : [
                BoxShadow(
                  color: const Color(0xFFE94057).withOpacity(0.35),
                  blurRadius: 15,
                  offset: const Offset(0, 5),
                )
              ],
      ),
      child: ElevatedButton(
        onPressed: isLoading ? null : generateTryOn,
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.transparent,
          shadowColor: Colors.transparent,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
        ),
        child: isLoading
            ? Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                  ),
                  const SizedBox(width: 15),
                  Text(
                    loadingProgressText,
                    style: const TextStyle(color: Colors.white, fontSize: 15, fontWeight: FontWeight.bold),
                  ),
                ],
              )
            : const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.auto_awesome, color: Colors.white),
                  SizedBox(width: 10),
                  Text(
                    "Generate AI Try-On",
                    style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ],
              ),
      ),
    );
  }

  Widget _buildEmptyResultCard() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 40, horizontal: 20),
      decoration: BoxDecoration(
        color: const Color(0xFF1E1B38).withOpacity(0.5),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFF322E54)),
      ),
      child: const Column(
        children: [
          Icon(Icons.image_search, size: 48, color: Color(0xFF5B5785)),
          const SizedBox(height: 12),
          Text(
            "Try-On result will appear here",
            style: TextStyle(color: Color(0xFF8B87B5), fontSize: 13, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }

  Widget _buildResultCard() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        const Row(
          children: [
            Icon(Icons.auto_awesome, color: Color(0xFFF27121), size: 20),
            SizedBox(width: 8),
            Text("Try-On Result", style: TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
          ],
        ),
        const SizedBox(height: 16),
        Container(
          decoration: BoxDecoration(
            color: const Color(0xFF1E1B38),
            borderRadius: BorderRadius.circular(20),
            border: Border.all(color: const Color(0xFFE94057), width: 1.5),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFFE94057).withOpacity(0.25),
                blurRadius: 20,
                spreadRadius: 1,
              )
            ],
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(18),
            child: Image.network(
              resultImage!.startsWith('http') ? resultImage! : "${ApiService.baseUrl.replaceAll('/api', '')}$resultImage",
              fit: BoxFit.cover,
              loadingBuilder: (context, child, loadingProgress) {
                if (loadingProgress == null) return child;
                return const SizedBox(
                  height: 300,
                  child: Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        CircularProgressIndicator(color: Color(0xFFE94057)),
                        SizedBox(height: 12),
                        Text("Loading AI Output...", style: TextStyle(color: Color(0xFF8B87B5), fontSize: 12)),
                      ],
                    ),
                  ),
                );
              },
              errorBuilder: (context, error, stackTrace) {
                return Container(
                  height: 250,
                  color: const Color(0xFF2E1B2A),
                  child: const Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.error_outline, color: Colors.redAccent, size: 40),
                        SizedBox(height: 12),
                        Text(
                          "Failed to render try-on image.\nVerify your server URL or network status.",
                          textAlign: TextAlign.center,
                          style: TextStyle(color: Colors.redAccent, fontSize: 12, height: 1.5),
                        ),
                      ],
                    ),
                  ),
                );
              },
            ),
          ),
        ),
      ],
    );
  }
}