import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'admin/admin_dashboard_screen.dart';
import 'user/user_dashboard_screen.dart';

class LoginRegisterScreen extends StatefulWidget {
  const LoginRegisterScreen({super.key});

  @override
  State<LoginRegisterScreen> createState() => _LoginRegisterScreenState();
}

class _LoginRegisterScreenState extends State<LoginRegisterScreen> {
  final _formKey = GlobalKey<FormState>();
  bool isLogin = true;
  String selectedRole = 'User'; // 'User' or 'Admin'

  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  String selectedCountryCode = '+92';

  bool isLoading = false;
  bool _obscurePassword = true;

  Future<void> _submitForm() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() {
      isLoading = true;
    });

    try {
      if (isLogin) {
        // Authenticate
        final response = await ApiService.login(
          _emailController.text.trim(),
          _passwordController.text,
          selectedRole,
        );

        if (response['status'] == 'success') {
          final String finalRole = response['role'];
          
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(response['message'] ?? 'Logged in!'),
              backgroundColor: Colors.green,
            ),
          );

          // Route to proper dashboard
          if (finalRole == 'Admin') {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const AdminDashboardScreen()),
            );
          } else {
            Navigator.pushReplacement(
              context,
              MaterialPageRoute(builder: (_) => const UserDashboardScreen()),
            );
          }
        } else {
          _showError(response['message'] ?? 'Authentication failed.');
        }
      } else {
        // Register
        final response = await ApiService.register(
          _nameController.text.trim(),
          _emailController.text.trim(),
          selectedCountryCode + _phoneController.text.trim(),
          _passwordController.text,
          selectedRole,
        );

        if (response['status'] == 'success') {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(response['message'] ?? 'Registered successfully! Please login.'),
              backgroundColor: Colors.green,
            ),
          );
          setState(() {
            isLogin = true;
          });
        } else {
          _showError(response['message'] ?? 'Registration failed.');
        }
      }
    } catch (e) {
      _showError("An unexpected error occurred: $e");
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
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
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
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 28.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // App Brand Logo/Icon
                  const Center(
                    child: CircleAvatar(
                      radius: 46,
                      backgroundColor: Color(0xFF1E1B38),
                      child: Icon(
                        Icons.auto_awesome,
                        size: 48,
                        color: Color(0xFFE94057),
                      ),
                    ),
                  ),
                  const SizedBox(height: 24),
                  
                  // Premium Title Glimmer
                  Center(
                    child: ShaderMask(
                      shaderCallback: (bounds) => const LinearGradient(
                        colors: [Color(0xFF8A2387), Color(0xFFE94057), Color(0xFFF27121)],
                      ).createShader(bounds),
                      child: const Text(
                        "DRESS TRY-ON",
                        style: TextStyle(
                          fontSize: 30,
                          fontWeight: FontWeight.w900,
                          color: Colors.white,
                          letterSpacing: 2.5,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  const Center(
                    child: Text(
                      "Your Personalized AI Fashion Studio",
                      style: TextStyle(color: Colors.grey, fontSize: 13),
                    ),
                  ),
                  const SizedBox(height: 35),

                  // Login/Register Form Card
                  Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: const Color(0xFF1E1B38),
                      borderRadius: BorderRadius.circular(24),
                      border: Border.all(color: const Color(0xFF322E54), width: 1.5),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.4),
                          blurRadius: 20,
                          offset: const Offset(0, 10),
                        ),
                      ],
                    ),
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.stretch,
                        children: [
                          // Tab Selector (Login / Register)
                          Row(
                            children: [
                              Expanded(
                                child: InkWell(
                                  onTap: () => setState(() => isLogin = true),
                                  child: Column(
                                    children: [
                                      Text(
                                        "Login",
                                        style: TextStyle(
                                          color: isLogin ? const Color(0xFFE94057) : Colors.grey,
                                          fontSize: 16,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(height: 6),
                                      Container(
                                        height: 2.5,
                                        color: isLogin ? const Color(0xFFE94057) : Colors.transparent,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              Expanded(
                                child: InkWell(
                                  onTap: () => setState(() => isLogin = false),
                                  child: Column(
                                    children: [
                                      Text(
                                        "Register",
                                        style: TextStyle(
                                          color: !isLogin ? const Color(0xFFE94057) : Colors.grey,
                                          fontSize: 16,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(height: 6),
                                      Container(
                                        height: 2.5,
                                        color: !isLogin ? const Color(0xFFE94057) : Colors.transparent,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 24),

                          // Role Dropdown Selection
                          const Text(
                            "Select Your Role",
                            style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.bold),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 16),
                            decoration: BoxDecoration(
                              color: const Color(0xFF0F0C20),
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: const Color(0xFF322E54)),
                            ),
                            child: DropdownButtonHideUnderline(
                              child: DropdownButton<String>(
                                value: selectedRole,
                                dropdownColor: const Color(0xFF1E1B38),
                                icon: const Icon(Icons.arrow_drop_down, color: Color(0xFFE94057)),
                                style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                                isExpanded: true,
                                items: <String>['User', 'Admin'].map((String value) {
                                  return DropdownMenuItem<String>(
                                    value: value,
                                    child: Text(value),
                                  );
                                }).toList(),
                                onChanged: (val) {
                                  if (val != null) {
                                    setState(() {
                                      selectedRole = val;
                                    });
                                  }
                                },
                              ),
                            ),
                          ),
                          const SizedBox(height: 20),

                          // Name field (Visible only on Register)
                          if (!isLogin) ...[
                            TextFormField(
                              controller: _nameController,
                              style: const TextStyle(color: Colors.white),
                              decoration: _buildInputDecoration("Full Name", Icons.person_outline),
                              validator: (val) {
                                if (val == null || val.trim().isEmpty) return "Please enter your name";
                                return null;
                              },
                            ),
                            const SizedBox(height: 16),
                            // Phone Number Field
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 12),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF0F0C20),
                                    borderRadius: BorderRadius.circular(12),
                                    border: Border.all(color: const Color(0xFF322E54)),
                                  ),
                                  child: DropdownButtonHideUnderline(
                                    child: DropdownButton<String>(
                                      value: selectedCountryCode,
                                      dropdownColor: const Color(0xFF1E1B38),
                                      style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                                      icon: const Icon(Icons.arrow_drop_down, color: Color(0xFFE94057)),
                                      items: ['+92', '+1', '+44', '+91', '+971'].map((String value) {
                                        return DropdownMenuItem<String>(
                                          value: value,
                                          child: Text(value),
                                        );
                                      }).toList(),
                                      onChanged: (val) {
                                        if (val != null) {
                                          setState(() {
                                            selectedCountryCode = val;
                                          });
                                        }
                                      },
                                    ),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: TextFormField(
                                    controller: _phoneController,
                                    style: const TextStyle(color: Colors.white),
                                    keyboardType: TextInputType.phone,
                                    decoration: _buildInputDecoration("Phone Number", Icons.phone),
                                    validator: (val) {
                                      if (val == null || val.trim().isEmpty) return "Please enter phone number";
                                      return null;
                                    },
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 16),
                          ],

                          // Email Field
                          TextFormField(
                            controller: _emailController,
                            style: const TextStyle(color: Colors.white),
                            keyboardType: TextInputType.emailAddress,
                            decoration: _buildInputDecoration("Email Address", Icons.email_outlined),
                            validator: (val) {
                              if (val == null || val.isEmpty) return "Please enter email";
                              if (!RegExp(r'^[^@]+@[^@]+\.[^@]+').hasMatch(val)) return "Invalid email address";
                              return null;
                            },
                          ),
                          const SizedBox(height: 16),

                          // Password Field
                          TextFormField(
                            controller: _passwordController,
                            style: const TextStyle(color: Colors.white),
                            obscureText: _obscurePassword,
                            decoration: _buildInputDecoration(
                              "Password",
                              Icons.lock_outline,
                              suffixIcon: IconButton(
                                icon: Icon(
                                  _obscurePassword ? Icons.visibility_off : Icons.visibility,
                                  color: const Color(0xFF8B87B5),
                                ),
                                onPressed: () {
                                  setState(() {
                                    _obscurePassword = !_obscurePassword;
                                  });
                                },
                              ),
                            ),
                            validator: (val) {
                              if (val == null || val.length < 6) return "Password must be at least 6 characters";
                              return null;
                            },
                          ),
                          const SizedBox(height: 28),

                          // Form Submit Button
                          Container(
                            height: 52,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(14),
                              gradient: isLoading
                                  ? null
                                  : const LinearGradient(
                                      colors: [Color(0xFF8A2387), Color(0xFFE94057), Color(0xFFF27121)],
                                    ),
                            ),
                            child: ElevatedButton(
                              onPressed: isLoading ? null : _submitForm,
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.transparent,
                                shadowColor: Colors.transparent,
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(14),
                                ),
                              ),
                              child: isLoading
                                  ? const SizedBox(
                                      width: 24,
                                      height: 24,
                                      child: CircularProgressIndicator(color: Colors.white, strokeWidth: 2.5),
                                    )
                                  : Text(
                                      isLogin ? "LOG IN" : "REGISTER",
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
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  InputDecoration _buildInputDecoration(String hint, IconData icon, {Widget? suffixIcon}) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(color: Color(0xFF5B5785), fontSize: 14),
      prefixIcon: Icon(icon, color: const Color(0xFF8B87B5)),
      suffixIcon: suffixIcon,
      filled: true,
      fillColor: const Color(0xFF0F0C20),
      contentPadding: const EdgeInsets.symmetric(vertical: 16),
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
