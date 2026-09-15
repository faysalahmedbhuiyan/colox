class AppUser {
  final int id;
  final String name;
  final String email;
  final String phone;
  final List<String> roles;

  AppUser({
    required this.id,
    required this.name,
    required this.email,
    required this.phone,
    required this.roles,
  });

  bool get isDriver => roles.contains('driver');
  bool get isRider => roles.contains('rider');
}