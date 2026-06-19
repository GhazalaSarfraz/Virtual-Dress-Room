import 'dart:io';
import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import '../../models/product.dart';
import '../../services/api_service.dart';

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
  String selectedCategory = 'Dresses'; // default category

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

  Future<void> _pickImage() async {
    final XFile? img = await picker.pickImage(source: ImageSource.gallery);
    if (img != null) {
      setState(() {
        selectedImage = img;
      });
    }
  }

  Future<void> _saveProduct() async {
    if (!_formKey.currentState!.validate()) return;
    
    // Image is strictly required ONLY for new product creation
    if (!isEditMode && selectedImage == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text("Please select a dress image!"),
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    setState(() {
      isLoading = true;
    });

    try {
      final double priceVal = double.tryParse(_priceController.text) ?? 0.0;
      Uint8List? imageBytes;
      String? filename;

      if (selectedImage != null) {
        imageBytes = await selectedImage!.readAsBytes();
        filename = selectedImage!.name;
        if (!filename!.toLowerCase().contains('.')) {
          filename = filename! + '.jpg';
        }
      }

      Map<String, dynamic> response;

      if (isEditMode) {
        // Execute Update
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
        // Execute Create/Add
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
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(response['message'] ?? 'Garment saved successfully!'),
            backgroundColor: Colors.green,
          ),
        );
        Navigator.pop(context, true); // Pop back with success indicator to refresh list
      } else {
        _showError(response['message'] ?? 'Failed to save garment.');
      }
    } catch (e) {
      _showError("Error saving product: $e");
    } finally {
      if (mounted) {
        setState(() {
          isLoading = false;
        });
      }
    }
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(msg),
        backgroundColor: Colors.redAccent,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0F0C20),
      appBar: AppBar(
        backgroundColor: const Color(0xFF1E1B38),
        iconTheme: const IconThemeData(color: Colors.white),
        title: Text(
          isEditMode ? "EDIT DRESS" : "UPLOAD DRESS",
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.bold,
            letterSpacing: 1.0,
            fontSize: 18,
          ),
        ),
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 20),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Image Picker Box
              InkWell(
                onTap: isLoading ? null : _pickImage,
                borderRadius: BorderRadius.circular(16),
                child: Container(
                  height: 220,
                  decoration: BoxDecoration(
                    color: const Color(0xFF1E1B38),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: (selectedImage != null || isEditMode) ? const Color(0xFFE94057) : const Color(0xFF322E54),
                      width: 1.5,
                    ),
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(16),
                    child: selectedImage != null
                        ? Stack(
                            fit: StackFit.expand,
                            children: [
                              kIsWeb
                                  ? Image.network(selectedImage!.path, fit: BoxFit.cover)
                                  : Image.file(File(selectedImage!.path), fit: BoxFit.cover),
                              Container(
                                color: Colors.black.withOpacity(0.4),
                              ),
                              const Positioned(
                                bottom: 12,
                                right: 12,
                                child: CircleAvatar(
                                  backgroundColor: Color(0xFFE94057),
                                  radius: 18,
                                  child: Icon(Icons.edit, color: Colors.white, size: 16),
                                ),
                              ),
                            ],
                          )
                        : isEditMode
                            ? Stack(
                                fit: StackFit.expand,
                                children: [
                                  Image.network(widget.productToEdit!.imageUrl, fit: BoxFit.cover),
                                  Container(
                                    color: Colors.black.withOpacity(0.4),
                                  ),
                                  const Positioned(
                                    bottom: 12,
                                    right: 12,
                                    child: CircleAvatar(
                                      backgroundColor: Color(0xFFE94057),
                                      radius: 18,
                                      child: Icon(Icons.edit, color: Colors.white, size: 16),
                                    ),
                                  ),
                                ],
                              )
                            : Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  const Icon(
                                    Icons.cloud_upload_outlined,
                                    size: 50,
                                    color: Color(0xFF8B87B5),
                                  ),
                                  const SizedBox(height: 12),
                                  const Text(
                                    "Select Dress Garment",
                                    style: TextStyle(
                                      color: Color(0xFF8B87B5),
                                      fontWeight: FontWeight.bold,
                                      fontSize: 14,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  const Text(
                                    "JPEG, PNG, or WEBP supported",
                                    style: TextStyle(
                                      color: Color(0xFF5B5785),
                                      fontSize: 11,
                                    ),
                                  ),
                                ],
                              ),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Inputs
              TextFormField(
                controller: _nameController,
                style: const TextStyle(color: Colors.white),
                decoration: _buildInputDecoration("Dress Title (e.g., Summer Gown)", Icons.title),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) return "Please enter a title";
                  return null;
                },
              ),
              const SizedBox(height: 16),

              // Category Dropdown
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                decoration: BoxDecoration(
                  color: const Color(0xFF1E1B38),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFF322E54)),
                ),
                child: DropdownButtonHideUnderline(
                  child: DropdownButton<String>(
                    value: selectedCategory,
                    dropdownColor: const Color(0xFF1E1B38),
                    icon: const Icon(Icons.arrow_drop_down, color: Color(0xFF8B87B5)),
                    isExpanded: true,
                    style: const TextStyle(color: Colors.white, fontSize: 15),
                    items: ['Kids dress','Hoodies', 'Jackets', 'Dresses', 'Pants', 'T-Shirts'].map((String val) {
                      return DropdownMenuItem<String>(
                        value: val,
                        child: Text(val),
                      );
                    }).toList(),
                    onChanged: (val) {
                      if (val != null) {
                        setState(() {
                          selectedCategory = val;
                        });
                      }
                    },
                  ),
                ),
              ),
              const SizedBox(height: 16),

              TextFormField(
                controller: _priceController,
                style: const TextStyle(color: Colors.white),
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: _buildInputDecoration("Price (USD)", Icons.attach_money),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) return "Please enter pricing";
                  if (double.tryParse(val) == null) return "Invalid price number";
                  return null;
                },
              ),
              const SizedBox(height: 16),

              TextFormField(
                controller: _descController,
                maxLines: 4,
                style: const TextStyle(color: Colors.white),
                decoration: _buildInputDecoration("Garment details, fabric description...", Icons.description),
                validator: (val) {
                  if (val == null || val.trim().isEmpty) return "Please enter some details";
                  return null;
                },
              ),
              const SizedBox(height: 30),

              // Submit Button
              Container(
                height: 54,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(16),
                  gradient: isLoading
                      ? null
                      : const LinearGradient(
                          colors: [Color(0xFF8A2387), Color(0xFFE94057), Color(0xFFF27121)],
                        ),
                ),
                child: ElevatedButton(
                  onPressed: isLoading ? null : _saveProduct,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.transparent,
                    shadowColor: Colors.transparent,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(16),
                    ),
                  ),
                  child: isLoading
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                        )
                      : Text(
                          isEditMode ? "UPDATE & SAVE" : "UPLOAD & SAVE",
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 16,
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
    );
  }

  InputDecoration _buildInputDecoration(String hint, IconData icon) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(color: Color(0xFF5B5785), fontSize: 13),
      prefixIcon: Icon(icon, color: const Color(0xFF8B87B5)),
      filled: true,
      fillColor: const Color(0xFF1E1B38),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFF322E54)),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFFE94057), width: 1.5),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Colors.redAccent),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Colors.redAccent, width: 1.5),
      ),
    );
  }
}
