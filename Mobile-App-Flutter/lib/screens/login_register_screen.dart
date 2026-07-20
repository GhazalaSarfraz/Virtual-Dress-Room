import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'admin/admin_dashboard_screen.dart';
import 'user/user_dashboard_screen.dart';

class LoginRegisterScreen extends StatefulWidget {
  const LoginRegisterScreen({super.key});

  @override
  State<LoginRegisterScreen> createState() => _LoginRegisterScreenState();
}

class _LoginRegisterScreenState extends State<LoginRegisterScreen>
    with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  bool isLogin = true;
  String selectedRole = 'User';

  final TextEditingController _nameController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  final TextEditingController _phoneController = TextEditingController();
  String selectedCountryCode = '+92';

  bool isLoading = false;
  bool _obscurePassword = true;

  late AnimationController _animController;
  late Animation<double> _fadeAnim;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 800),
    );
    _fadeAnim = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    _nameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _phoneController.dispose();
    super.dispose();
  }

  Future<void> _submitForm() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => isLoading = true);

    try {
      if (isLogin) {
        final response = await ApiService.login(
          _emailController.text.trim(),
          _passwordController.text,
          selectedRole,
        );

        if (response['status'] == 'success') {
          final String finalRole = response['role'];
          if (!mounted) return;
          _showSuccess(response['message'] ?? 'Welcome back! ✨');

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
        final response = await ApiService.register(
          _nameController.text.trim(),
          _emailController.text.trim(),
          selectedCountryCode + _phoneController.text.trim(),
          _passwordController.text,
          selectedRole,
        );

        if (response['status'] == 'success') {
          if (!mounted) return;
          _showSuccess(response['message'] ?? 'Registered! Please login.');
          setState(() => isLogin = true);
        } else {
          _showError(response['message'] ?? 'Registration failed.');
        }
      }
    } catch (e) {
      _showError("An unexpected error occurred: $e");
    } finally {
      if (mounted) setState(() => isLoading = false);
    }
  }

  void _showError(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Row(children: [
        const Icon(Icons.error_outline, color: Colors.white, size: 18),
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

  void _showSuccess(String msg) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Row(children: [
        const Icon(Icons.check_circle_outline, color: Colors.black, size: 18),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            msg,
            style: const TextStyle(color: Colors.black, fontWeight: FontWeight.w500),
          ),
        ),
      ]),
      backgroundColor: VDRTheme.borderGlow,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
    ));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        height: double.infinity,
        decoration: const BoxDecoration(
          color: Colors.white,
        ),
        child: SafeArea(
          child: FadeTransition(
            opacity: _fadeAnim,
            child: Center(
              child: SingleChildScrollView(
                padding: const EdgeInsets.symmetric(horizontal: 26, vertical: 20),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    // ── Logo ──────────────────────────────────────
                    Container(
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: VDRTheme.primary.withOpacity(0.5),
                            blurRadius: 35,
                            spreadRadius: 4,
                          ),
                        ],
                      ),
                      child: ClipOval(
                        child: Image.asset(
                          'assets/logo.png',
                          width: 100,
                          height: 100,
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                    const SizedBox(height: 22),

                    // ── App Name ─────────────────────────────────
                    ShaderMask(
                      shaderCallback: (b) => VDRTheme.mainGradient.createShader(b),
                      child: const Text(
                        "VIRTUAL DRESS ROOM",
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.w900,
                          color: Colors.black,
                          letterSpacing: 2.0,
                        ),
                      ),
                    ),
                    const SizedBox(height: 6),
                    const Text(
                      "Your AI-Powered Fashion Studio ✨",
                      style: TextStyle(
                        color: VDRTheme.textSub,
                        fontSize: 13,
                        letterSpacing: 0.5,
                      ),
                    ),
                    const SizedBox(height: 36),

                    // ── Form Card ────────────────────────────────
                    Container(
                      padding: const EdgeInsets.all(24),
                      decoration: BoxDecoration(
                        color: VDRTheme.bgCard,
                        borderRadius: BorderRadius.circular(28),
                        border: Border.all(
                          color: VDRTheme.borderGlow.withOpacity(0.35),
                          width: 1.5,
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: VDRTheme.primary.withOpacity(0.12),
                            blurRadius: 30,
                            offset: const Offset(0, 12),
                          ),
                          BoxShadow(
                            color: Colors.black.withOpacity(0.08),
                            blurRadius: 20,
                            offset: const Offset(0, 8),
                          ),
                        ],
                      ),
                      child: Form(
                        key: _formKey,
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            // ── Tab: Login / Register ─────────────
                            Container(
                              decoration: BoxDecoration(
                                color: VDRTheme.bgDark,
                                borderRadius: BorderRadius.circular(16),
                                border: Border.all(color: VDRTheme.border),
                              ),
                              child: Row(
                                children: [
                                  _buildTab("Login", isLogin, () {
                                    setState(() => isLogin = true);
                                    _animController.forward(from: 0.5);
                                  }),
                                  _buildTab("Register", !isLogin, () {
                                    setState(() => isLogin = false);
                                    _animController.forward(from: 0.5);
                                  }),
                                ],
                              ),
                            ),
                            const SizedBox(height: 24),

                            // ── Role Selector ────────────────────
                            _buildLabel("Select Role"),
                            const SizedBox(height: 8),
                            _buildDropdown(
                              value: selectedRole,
                              items: ['User', 'Admin'],
                              icon: Icons.person_pin_outlined,
                              onChanged: (val) => setState(() => selectedRole = val!),
                            ),
                            const SizedBox(height: 18),

                            // ── Register-only fields ─────────────
                            if (!isLogin) ...[
                              TextFormField(
                                controller: _nameController,
                                style: const TextStyle(color: Colors.black),
                                decoration: _inputDecoration("Full Name", Icons.person_outline),
                                validator: (val) =>
                                    (val == null || val.trim().isEmpty) ? "Enter your name" : null,
                              ),
                              const SizedBox(height: 14),
                              Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  _buildCountryCodePicker(),
                                  const SizedBox(width: 10),
                                  Expanded(
                                    child: TextFormField(
                                      controller: _phoneController,
                                      style: const TextStyle(color: Colors.black),
                                      keyboardType: TextInputType.phone,
                                      decoration: _inputDecoration("Phone Number", Icons.phone_outlined),
                                      validator: (val) =>
                                          (val == null || val.trim().isEmpty) ? "Enter phone number" : null,
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 14),
                            ],

                            // ── Email ────────────────────────────
                            TextFormField(
                              controller: _emailController,
                              style: const TextStyle(color: Colors.black),
                              keyboardType: TextInputType.emailAddress,
                              decoration: _inputDecoration("Email Address", Icons.email_outlined),
                              validator: (val) {
                                if (val == null || val.isEmpty) return "Enter email";
                                if (!RegExp(r'^[^@]+@[^@]+\.[^@]+').hasMatch(val)) return "Invalid email";
                                return null;
                              },
                            ),
                            const SizedBox(height: 14),

                            // ── Password ─────────────────────────
                            TextFormField(
                              controller: _passwordController,
                              style: const TextStyle(color: Colors.black),
                              obscureText: _obscurePassword,
                              decoration: _inputDecoration(
                                "Password",
                                Icons.lock_outline,
                                suffix: IconButton(
                                  icon: Icon(
                                    _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                                    color: VDRTheme.textSub,
                                    size: 20,
                                  ),
                                  onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                                ),
                              ),
                              validator: (val) =>
                                  (val == null || val.length < 6) ? "Min 6 characters" : null,
                            ),
                            const SizedBox(height: 28),

                            // ── Submit Button ─────────────────────
                            Container(
                              height: 54,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(16),
                                gradient: isLoading ? null : VDRTheme.mainGradient,
                                color: isLoading ? VDRTheme.bgCard2 : null,
                                boxShadow: isLoading
                                    ? []
                                    : [
                                        BoxShadow(
                                          color: VDRTheme.primary.withOpacity(0.5),
                                          blurRadius: 20,
                                          offset: const Offset(0, 7),
                                        ),
                                      ],
                              ),
                              child: ElevatedButton(
                                onPressed: isLoading ? null : _submitForm,
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.transparent,
                                  shadowColor: Colors.transparent,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                ),
                                child: isLoading
                                    ? const SizedBox(
                                        width: 22,
                                        height: 22,
                                        child: CircularProgressIndicator(
                                            color: Colors.white, strokeWidth: 2.5),
                                      )
                                    : Text(
                                        isLogin ? "LOGIN  →" : "CREATE ACCOUNT  →",
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 15,
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

                    const SizedBox(height: 24),
                    // Footer text
                    Text(
                      "© 2025 Virtual Dress Room · AI Fashion Studio",
                      style: TextStyle(
                        color: VDRTheme.textMuted.withOpacity(0.6),
                        fontSize: 11,
                      ),
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }

  // ── Helpers ─────────────────────────────────────────────────────

  Widget _buildTab(String label, bool active, VoidCallback onTap) {
    return Expanded(
      child: GestureDetector(
        onTap: onTap,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 250),
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            gradient: active ? VDRTheme.mainGradient : null,
            borderRadius: BorderRadius.circular(14),
            boxShadow: active
                ? [BoxShadow(color: VDRTheme.primary.withOpacity(0.4), blurRadius: 12, offset: const Offset(0, 3))]
                : [],
          ),
          child: Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
              color: active ? Colors.white : VDRTheme.textMuted,
              fontWeight: active ? FontWeight.bold : FontWeight.normal,
              fontSize: 14,
            ),
          ),
        ),
      ),
    );
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

  Widget _buildDropdown({
    required String value,
    required List<String> items,
    required IconData icon,
    required ValueChanged<String?> onChanged,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14),
      decoration: BoxDecoration(
        color: VDRTheme.bgDark,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: VDRTheme.border),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: value,
          dropdownColor: VDRTheme.bgCard2,
          icon: const Icon(Icons.keyboard_arrow_down_rounded, color: VDRTheme.primary),
          style: const TextStyle(color: Colors.black, fontWeight: FontWeight.bold, fontSize: 14),
          isExpanded: true,
          items: items.map((v) => DropdownMenuItem<String>(value: v, child: Text(v))).toList(),
          onChanged: onChanged,
        ),
      ),
    );
  }

  Widget _buildCountryCodePicker() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10),
      decoration: BoxDecoration(
        color: VDRTheme.bgDark,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: VDRTheme.border),
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: selectedCountryCode,
          dropdownColor: VDRTheme.bgCard2,
          style: const TextStyle(color: Colors.black, fontWeight: FontWeight.bold, fontSize: 13),
          icon: const Icon(Icons.arrow_drop_down, color: VDRTheme.primary, size: 18),
          items: ['+92', '+1', '+44', '+91', '+971']
              .map((v) => DropdownMenuItem<String>(value: v, child: Text(v)))
              .toList(),
          onChanged: (val) => setState(() => selectedCountryCode = val!),
        ),
      ),
    );
  }

  InputDecoration _inputDecoration(String hint, IconData icon, {Widget? suffix}) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(color: VDRTheme.textMuted, fontSize: 14),
      prefixIcon: Icon(icon, color: VDRTheme.textSub, size: 20),
      suffixIcon: suffix,
      filled: true,
      fillColor: VDRTheme.bgDark,
      contentPadding: const EdgeInsets.symmetric(vertical: 16),
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
