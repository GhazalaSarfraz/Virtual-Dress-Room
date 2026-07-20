import 'dart:math';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'login_register_screen.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {
  // ── Animation Controllers ──────────────────────────────────────────
  late AnimationController _logoController;
  late AnimationController _textController;
  late AnimationController _shimmerController;
  late AnimationController _particleController;
  late AnimationController _pulseController;

  // ── Animations ────────────────────────────────────────────────────
  late Animation<double> _logoScale;
  late Animation<double> _logoOpacity;
  late Animation<double> _logoRotate;
  late Animation<double> _textOpacity;
  late Animation<Offset> _textSlide;
  late Animation<double> _taglineOpacity;
  late Animation<Offset> _taglineSlide;
  late Animation<double> _shimmer;
  late Animation<double> _pulse;

  static const Color _gold = Color(0xFFD4AF37);
  static const Color _darkCharcoal = Color(0xFF2F2F2F);
  static const Color _softIvory = Color(0xFFFAF9F6);
  static const Color _lightGold = Color(0xFFE5C86C);

  @override
  void initState() {
    super.initState();

    SystemChrome.setSystemUIOverlayStyle(const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
    ));

    // Logo controller - handles scale, opacity, and slight rotation
    _logoController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    );

    // Text controller - handles brand name + tagline
    _textController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    );

    // Shimmer on the gold bar
    _shimmerController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1800),
    )..repeat();

    // Particle float animation
    _particleController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 3000),
    )..repeat(reverse: true);

    // Pulse glow on logo
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);

    // ── Define Animations ──────────────────────────────────────────
    _logoScale = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _logoController, curve: Curves.elasticOut),
    );

    _logoOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _logoController,
        curve: const Interval(0.0, 0.5, curve: Curves.easeIn),
      ),
    );

    _logoRotate = Tween<double>(begin: -0.15, end: 0.0).animate(
      CurvedAnimation(parent: _logoController, curve: Curves.elasticOut),
    );

    _textOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _textController,
        curve: const Interval(0.0, 0.7, curve: Curves.easeOut),
      ),
    );

    _textSlide = Tween<Offset>(
      begin: const Offset(0, 0.4),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(parent: _textController, curve: Curves.easeOutCubic),
    );

    _taglineOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _textController,
        curve: const Interval(0.4, 1.0, curve: Curves.easeOut),
      ),
    );

    _taglineSlide = Tween<Offset>(
      begin: const Offset(0, 0.6),
      end: Offset.zero,
    ).animate(
      CurvedAnimation(
        parent: _textController,
        curve: const Interval(0.4, 1.0, curve: Curves.easeOutCubic),
      ),
    );

    _shimmer = Tween<double>(begin: -2.0, end: 2.0).animate(
      CurvedAnimation(parent: _shimmerController, curve: Curves.easeInOut),
    );

    _pulse = Tween<double>(begin: 0.95, end: 1.05).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    // ── Sequence ──────────────────────────────────────────────────
    _startAnimationSequence();
  }

  Future<void> _startAnimationSequence() async {
    await Future.delayed(const Duration(milliseconds: 300));
    await _logoController.forward();
    await Future.delayed(const Duration(milliseconds: 200));
    await _textController.forward();
    await Future.delayed(const Duration(milliseconds: 1800));
    _navigateToLogin();
  }

  void _navigateToLogin() {
    if (!mounted) return;
    Navigator.of(context).pushReplacement(
      PageRouteBuilder(
        transitionDuration: const Duration(milliseconds: 700),
        pageBuilder: (_, __, ___) => const LoginRegisterScreen(),
        transitionsBuilder: (_, animation, __, child) {
          return FadeTransition(
            opacity: CurvedAnimation(parent: animation, curve: Curves.easeOut),
            child: child,
          );
        },
      ),
    );
  }

  @override
  void dispose() {
    _logoController.dispose();
    _textController.dispose();
    _shimmerController.dispose();
    _particleController.dispose();
    _pulseController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: _softIvory,
      body: Stack(
        children: [
          // ── Background decorative circles ──────────────────────
          Positioned(
            top: -80,
            right: -60,
            child: AnimatedBuilder(
              animation: _particleController,
              builder: (_, __) => Opacity(
                opacity: 0.08,
                child: Container(
                  width: 280,
                  height: 280,
                  decoration: const BoxDecoration(
                    shape: BoxShape.circle,
                    color: _gold,
                  ),
                ),
              ),
            ),
          ),
          Positioned(
            bottom: -100,
            left: -80,
            child: AnimatedBuilder(
              animation: _particleController,
              builder: (_, __) => Opacity(
                opacity: 0.06,
                child: Container(
                  width: 320,
                  height: 320,
                  decoration: const BoxDecoration(
                    shape: BoxShape.circle,
                    color: _gold,
                  ),
                ),
              ),
            ),
          ),

          // ── Floating Sparkle Particles ─────────────────────────
          ..._buildParticles(size),

          // ── Main Content ───────────────────────────────────────
          Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // ── Logo with pulse glow ─────────────────────────
                AnimatedBuilder(
                  animation: Listenable.merge([
                    _logoController,
                    _pulseController,
                  ]),
                  builder: (_, __) {
                    return Opacity(
                      opacity: _logoOpacity.value,
                      child: Transform.scale(
                        scale: _logoScale.value,
                        child: Transform.rotate(
                          angle: _logoRotate.value,
                          child: Stack(
                            alignment: Alignment.center,
                            children: [
                              // Glow ring
                              Transform.scale(
                                scale: _pulse.value,
                                child: Container(
                                  width: 170,
                                  height: 170,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    boxShadow: [
                                      BoxShadow(
                                        color: _gold.withOpacity(0.25),
                                        blurRadius: 50,
                                        spreadRadius: 15,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              // White circle background
                              Container(
                                width: 148,
                                height: 148,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: Colors.white,
                                  border: Border.all(
                                    color: _gold.withOpacity(0.3),
                                    width: 2,
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: _gold.withOpacity(0.15),
                                      blurRadius: 30,
                                      spreadRadius: 5,
                                    ),
                                    BoxShadow(
                                      color: Colors.black.withOpacity(0.06),
                                      blurRadius: 20,
                                      offset: const Offset(0, 8),
                                    ),
                                  ],
                                ),
                                child: ClipOval(
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Image.asset(
                                      'assets/logo.png',
                                      fit: BoxFit.contain,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ),

                const SizedBox(height: 40),

                // ── Brand Name ───────────────────────────────────
                AnimatedBuilder(
                  animation: _textController,
                  builder: (_, __) => SlideTransition(
                    position: _textSlide,
                    child: FadeTransition(
                      opacity: _textOpacity,
                      child: Column(
                        children: [
                          // Shimmer gold bar
                          AnimatedBuilder(
                            animation: _shimmerController,
                            builder: (_, __) => ShaderMask(
                              shaderCallback: (bounds) => LinearGradient(
                                colors: const [
                                  _gold,
                                  _lightGold,
                                  Colors.white,
                                  _lightGold,
                                  _gold,
                                ],
                                stops: const [0.0, 0.3, 0.5, 0.7, 1.0],
                                begin: Alignment(_shimmer.value - 1, 0),
                                end: Alignment(_shimmer.value + 1, 0),
                              ).createShader(bounds),
                              child: const Text(
                                "VIRTUAL DRESS ROOM",
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 22,
                                  fontWeight: FontWeight.w900,
                                  letterSpacing: 3.5,
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(height: 10),

                          // Gold divider line
                          Container(
                            width: 60,
                            height: 1.5,
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [Colors.transparent, _gold, Colors.transparent],
                              ),
                              borderRadius: BorderRadius.circular(2),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),

                const SizedBox(height: 14),

                // ── Tagline ──────────────────────────────────────
                AnimatedBuilder(
                  animation: _textController,
                  builder: (_, __) => SlideTransition(
                    position: _taglineSlide,
                    child: FadeTransition(
                      opacity: _taglineOpacity,
                      child: const Text(
                        "AI-Powered Fashion Studio ✨",
                        style: TextStyle(
                          color: Color(0xFF888888),
                          fontSize: 13,
                          letterSpacing: 0.8,
                          fontWeight: FontWeight.w400,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),

          // ── Bottom loading dots ────────────────────────────────
          Positioned(
            bottom: 60,
            left: 0,
            right: 0,
            child: AnimatedBuilder(
              animation: _textController,
              builder: (_, __) => FadeTransition(
                opacity: _taglineOpacity,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(3, (i) {
                    return AnimatedBuilder(
                      animation: _shimmerController,
                      builder: (_, __) {
                        final delay = i * 0.33;
                        final value = ((_shimmerController.value + delay) % 1.0);
                        final opacity = value < 0.5
                            ? value * 2
                            : (1.0 - value) * 2;
                        return Container(
                          margin: const EdgeInsets.symmetric(horizontal: 4),
                          width: 6,
                          height: 6,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            color: _gold.withOpacity(0.3 + opacity * 0.7),
                          ),
                        );
                      },
                    );
                  }),
                ),
              ),
            ),
          ),

          // ── Footer ────────────────────────────────────────────
          Positioned(
            bottom: 30,
            left: 0,
            right: 0,
            child: AnimatedBuilder(
              animation: _textController,
              builder: (_, __) => FadeTransition(
                opacity: _taglineOpacity,
                child: const Text(
                  "© 2025 Virtual Dress Room",
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: Color(0xFFAAAAAA),
                    fontSize: 10,
                    letterSpacing: 0.5,
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ── Floating Gold Particles ──────────────────────────────────────
  List<Widget> _buildParticles(Size size) {
    final rng = Random(42);
    return List.generate(12, (i) {
      final x = rng.nextDouble() * size.width;
      final y = rng.nextDouble() * size.height;
      final sz = rng.nextDouble() * 5 + 2;
      final delay = rng.nextDouble();

      return Positioned(
        left: x,
        top: y,
        child: AnimatedBuilder(
          animation: _particleController,
          builder: (_, __) {
            final anim = ((_particleController.value + delay) % 1.0);
            final offsetY = sin(anim * pi) * 12;
            return Transform.translate(
              offset: Offset(0, offsetY),
              child: Opacity(
                opacity: 0.2 + anim * 0.3,
                child: Container(
                  width: sz,
                  height: sz,
                  decoration: const BoxDecoration(
                    shape: BoxShape.circle,
                    color: _gold,
                  ),
                ),
              ),
            );
          },
        ),
      );
    });
  }
}
