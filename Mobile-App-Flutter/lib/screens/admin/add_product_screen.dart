import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';
import '../user/user_dashboard_screen.dart' show VDRTheme;

class AddProductScreen extends StatefulWidget {
  final Product? productToEdit;
  const AddProductScreen({super.key, this.productToEdit});

  @override
  State<AddProductScreen> createState() => _AddProductScreenState();
}

class _AddProductScreenState extends State<AddProductScreen> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _priceController = TextEditingController();
  final TextEditingController _descController = TextEditingController();
  String selectedCategory = 'Dresses';

  XFile? selectedImage;
  bool isLoading = false;
  final picker = ImagePicker();

  bool get isEditMode => widget.productToEdit != null;

  @override
  void initState() {
    super.initState();
    if (isEditMode) {
      final prod = widget.productToEdit!;
      _nameController.text = prod.name;
      _priceController.text = prod.price.toStringAsFixed(2);
      _descController.text = prod.description;
      selectedCategory = prod.category.isEmpty ? 'Dresses' : prod.category;
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _priceController.dispose();
    _descController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    final XFile? img = await picker.pickImage(source: ImageSource.gallery);
    if (img != null) setState(() => selectedImage = img);
  }

  Future<void> _saveProduct() async {
    if (!_formKey.currentState!.validate()) return;

    if (!isEditMode && selectedImage == null) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: const Row(children: [
          Icon(Icons.image_not_supported_outlined, color: Colors.white, size: 16),
          SizedBox(width: 8),
          Text("Please select a dress image!"),
        ]),
        backgroundColor: Colors.redAccent,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      ));
      return;
    }

    setState(() => isLoading = true);

    try {
      final double priceVal = double.tryParse(_priceController.text) ?? 0.0;
      Uint8List? imageBytes;
      String? filename;

      if (selectedImage != null) {
        imageBytes = await selectedImage!.readAsBytes();
        filename = selectedImage!.name;
        if (!filename.toLowerCase().contains('.')) filename = '$filename.jpg';
      }

      Map<String, dynamic> response;

      if (isEditMode) {
        response = await ApiService.updateProductMultipart(
          id: widget.productToEdit!.id,
          name: _nameController.text.trim(),
          description: _descController.text.trim(),
          price: priceVal,
          category: selectedCategory,
          imageBytes: imageBytes,
          filename: filename,
        );
      } else {
        response = await ApiService.uploadProductMultipart(
          name: _nameController.text.trim(),
          description: _descController.text.trim(),
          price: priceVal,
          category: selectedCategory,
          imageBytes: imageBytes!,
          filename: filename!,
        );
      }

      if (response['status'] == 'success') {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(
          content: Row(children: [
            const Icon(Icons.check_circle, color: Colors.white, size: 16),
            const SizedBox(width: 8),
            Expanded(
              child: Text(
                response['message'] ?? 'Saved successfully!',
                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w500),
              ),
            ),
          ]),
          backgroundColor: VDRTheme.borderGlow,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        ));
        Navigator.pop(context, true);
      } else {
        _showError(response['message'] ?? 'Failed to save.');
      }
    } catch (e) {
      _showError("Error: $e");
    } finally {
      if (mounted) setState(() => isLoading = false);
    }
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Row(children: [
        const Icon(Icons.error_outline, color: Colors.white, size: 16),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            msg,
            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w500),
          ),
        ),
      ]),
      backgroundColor: Colors.redAccent,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: VDRTheme.bgDark,
      appBar: AppBar(
        backgroundColor: VDRTheme.bgCard,
        iconTheme: const IconThemeData(color: Color(0xFF2F2F2F)),
        elevation: 0,
        title: ShaderMask(
          shaderCallback: (b) => VDRTheme.mainGradient.createShader(b),
          child: Text(
            isEditMode ? "EDIT GARMENT" : "UPLOAD GARMENT",
            style: const TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w900,
              letterSpacing: 1.2,
              fontSize: 17,
            ),
          ),
        ),
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: VDRTheme.border),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 22, vertical: 20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // ── Image Picker ────────────────────────────────────
              GestureDetector(
                onTap: isLoading ? null : _pickImage,
                child: Container(
                  height: 230,
                  decoration: BoxDecoration(
                    color: VDRTheme.bgCard2,
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: (selectedImage != null || isEditMode)
                          ? VDRTheme.borderGlow.withOpacity(0.7)
                          : VDRTheme.border,
                      width: 1.5,
                    ),
                    boxShadow: [
                      if (selectedImage != null || isEditMode)
                        BoxShadow(
                          color: VDRTheme.primary.withOpacity(0.2),
                          blurRadius: 20,
                          spreadRadius: 2,
                        ),
                    ],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(18),
                    child: _buildImagePickerContent(),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // ── Product Name ────────────────────────────────────
              _buildLabel("Dress Title"),
              const SizedBox(height: 8),
              TextFormField(
                controller: _nameController,
                style: const TextStyle(color: Color(0xFF2F2F2F)),
                decoration: _inputDecoration("e.g., Summer Gown, Elegant Jacket...", Icons.title_rounded),
                validator: (val) =>
                    (val == null || val.trim().isEmpty) ? "Please enter a title" : null,
              ),
              const SizedBox(height: 18),

              // ── Category ─────────────────────────────────────────
              _buildLabel("Category"),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 2),
                decoration: BoxDecoration(
                  color: VDRTheme.bgDark,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: VDRTheme.border),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: selectedCategory,
                    dropdownColor: VDRTheme.bgCard2,
                    icon: const Icon(Icons.keyboard_arrow_down_rounded, color: VDRTheme.primary),
                    isExpanded: true,
                    style: const TextStyle(color: Color(0xFF2F2F2F), fontSize: 14),
                    items: ['Kids dress', 'Hoodies', 'Jackets', 'Dresses', 'Pants', 'T-Shirts', 'Traditional']
                        .map((v) => DropdownMenuItem<String>(value: v, child: Text(v)))
                        .toList(),
                    onChanged: (val) {
                      if (val != null) setState(() => selectedCategory = val);
                    },
                  ),
                ),
              ),
              const SizedBox(height: 18),

              // ── Price ────────────────────────────────────────────
              _buildLabel("Price (USD)"),
              const SizedBox(height: 8),
              TextFormField(
                controller: _priceController,
                style: const TextStyle(color: Color(0xFF2F2F2F)),
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: _inputDecoration("e.g., 49.99", Icons.attach_money_rounded),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) return "Enter a price";
                  if (double.tryParse(val) == null) return "Invalid number";
                  return null;
                },
              ),
              const SizedBox(height: 18),

              // ── Description ──────────────────────────────────────
              _buildLabel("Description"),
              const SizedBox(height: 8),
              TextFormField(
                controller: _descController,
                maxLines: 4,
                style: const TextStyle(color: Color(0xFF2F2F2F)),
                decoration: _inputDecoration("Fabric type, style details, fit info...", Icons.description_rounded),
                validator: (val) =>
                    (val == null || val.trim().isEmpty) ? "Please add a description" : null,
              ),
              const SizedBox(height: 30),

              // ── Submit Button ─────────────────────────────────────
              Container(
                height: 56,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(18),
                  gradient: isLoading ? null : VDRTheme.mainGradient,
                  color: isLoading ? VDRTheme.bgCard2 : null,
                  boxShadow: isLoading
                      ? []
                      : [
                          BoxShadow(
                            color: VDRTheme.primary.withOpacity(0.5),
                            blurRadius: 22,
                            offset: const Offset(0, 7),
                          ),
                        ],
                ),
                child: ElevatedButton(
                  onPressed: isLoading ? null : _saveProduct,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.transparent,
                    shadowColor: Colors.transparent,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
                  ),
                  child: isLoading
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                        )
                      : Text(
                          isEditMode ? "UPDATE & SAVE  ✓" : "UPLOAD & PUBLISH  ✨",
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 15,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 0.8,
                          ),
                        ),
                ),
              ),
              const SizedBox(height: 30),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildImagePickerContent() {
    if (selectedImage != null) {
      return Stack(
        fit: StackFit.expand,
        children: [
          kIsWeb
              ? Image.network(selectedImage!.path, fit: BoxFit.cover)
              : Image.file(File(selectedImage!.path), fit: BoxFit.cover),
          Container(color: Colors.black.withOpacity(0.35)),
          Positioned(
            bottom: 12, right: 12,
            child: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                gradient: VDRTheme.mainGradient,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(color: VDRTheme.primary.withOpacity(0.5), blurRadius: 10),
                ],
              ),
              child: const Icon(Icons.edit_rounded, color: Colors.white, size: 16),
            ),
          ),
          const Positioned(
            top: 12, left: 0, right: 0,
            child: Center(
              child: Text("Tap to change image",
                  style: TextStyle(color: Colors.white70, fontSize: 11)),
            ),
          ),
        ],
      );
    } else if (isEditMode) {
      return Stack(
        fit: StackFit.expand,
        children: [
          Image.network(widget.productToEdit!.imageUrl, fit: BoxFit.cover),
          Container(color: Colors.black.withOpacity(0.4)),
          Positioned(
            bottom: 12, right: 12,
            child: Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                gradient: VDRTheme.mainGradient,
                shape: BoxShape.circle,
              ),
              child: const Icon(Icons.edit_rounded, color: Colors.white, size: 16),
            ),
          ),
          const Positioned(
            top: 12, left: 0, right: 0,
            child: Center(
              child: Text("Current image — Tap to replace",
                  style: TextStyle(color: Colors.white70, fontSize: 11)),
            ),
          ),
        ],
      );
    } else {
      return Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            padding: const EdgeInsets.all(18),
            decoration: BoxDecoration(
              color: VDRTheme.bgCard,
              shape: BoxShape.circle,
              border: Border.all(color: VDRTheme.border),
            ),
            child: const Icon(Icons.cloud_upload_outlined, size: 40, color: VDRTheme.textSub),
          ),
          const SizedBox(height: 14),
          const Text(
            "Tap to Select Dress Image",
            style: TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
          ),
          const SizedBox(height: 6),
          const Text(
            "JPEG, PNG, or WEBP supported",
            style: TextStyle(color: VDRTheme.textMuted, fontSize: 11),
          ),
        ],
      );
    }
  }

  Widget _buildLabel(String text) {
    return Text(
      text,
      style: const TextStyle(
        color: VDRTheme.textSub,
        fontSize: 12,
        fontWeight: FontWeight.bold,
        letterSpacing: 0.5,
      ),
    );
  }

  InputDecoration _inputDecoration(String hint, IconData icon) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(color: VDRTheme.textMuted, fontSize: 13),
      prefixIcon: Icon(icon, color: VDRTheme.textSub, size: 20),
      filled: true,
      fillColor: VDRTheme.bgDark,
      contentPadding: const EdgeInsets.symmetric(vertical: 16, horizontal: 4),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: BorderSide(color: VDRTheme.border),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: BorderSide(color: VDRTheme.primary, width: 1.5),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(color: Colors.redAccent),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(14),
        borderSide: const BorderSide(color: Colors.redAccent, width: 1.5),
      ),
    );
  }
}
