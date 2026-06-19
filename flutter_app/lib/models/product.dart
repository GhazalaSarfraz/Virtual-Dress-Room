class Product {
  final dynamic id;
  final String name;
  final String imageUrl;
  final String description;
  final double price;
  final String category;

  Product({
    required this.id,
    required this.name,
    required this.imageUrl,
    required this.description,
    required this.price,
    this.category = 'Dresses',
  });

  // Factory to create a Product from a JSON Map
  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'] ?? '',
      imageUrl: json['image_url'] ?? '',
      description: json['description'] ?? '',
      price: double.tryParse(json['price']?.toString() ?? '0.0') ?? 0.0,
      category: json['category'] ?? 'Dresses',
    );
  }

  // Convert Product to a JSON Map
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'image_url': imageUrl,
      'description': description,
      'price': price,
      'category': category,
    };
  }
}
