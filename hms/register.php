<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hosteller Registration</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fontawesome-free-5.15.4-web/css/all.min.css"> 
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 800px;
            margin-top: 50px;
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            font-weight: bold;
        }

        #photo-preview {
            display: none;
            max-width: 200px;
            margin-top: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }

        .mt-3 a {
            color: #007bff;
        }

        .custom-file-input {
            cursor: pointer;
        }

        .custom-file-label::after {
            content: "Browse";
        }

        .custom-file-label[aria-selected="true"]::after {
            content: "Change";
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h2 class="mt-4 text-center"><i class="fas fa-user-plus"></i> Hosteller Registration</h2><br>
                <form id="registration-form" action="registration_process.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name"><i class="fas fa-user"></i> Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Mobile Number:</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="gender"><i class="fas fa-venus-mars"></i> Gender:</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address"><i class="fas fa-map-marker-alt"></i> Address:</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="dob"><i class="fas fa-calendar-alt"></i> Date of Birth:</label>
                        <input type="date" class="form-control" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="photo"><i class="fas fa-camera"></i> Photo:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*" required>
                            <label class="custom-file-label" for="photo">Choose file</label>
                        </div>
                        <img id="photo-preview" src="#" alt="Photo Preview">
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password"><i class="fas fa-lock"></i> Confirm Password:</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                    </div>
                    <div class="form-group">
                        <label for="hosteller-type"><i class="fas fa-users"></i> Hosteller Type:</label>
                        <select class="form-control" id="hosteller-type" name="hosteller-type" required>
                            <option value="Student">Student</option>
                            <option value="Employee">Employee</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="food"><i class="fas fa-utensils"></i> Food:</label>
                        <select class="form-control" id="food" name="food">
                            <option value="Required">Required</option>
                            <option value="Not Required">Not-Required</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-user-plus"></i> Register</button>
                </form>
                <div class="mt-3">
                    <p>Already have an account? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login here</a></p>
                </div>
            </div>
        </div>
    </div>
    <!-- AdminLTE JS -->
    <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/bootstrap-4.5.3-dist/js/bootstrap.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="assets/fontawesome-free-5.15.4-web/js/all.min.js"></script>
    <!-- jQuery -->
    <script src="assets/jquery-3.6.0.min.js"></script>
    <!-- Moment JS -->
    <script src="assets/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            // Preview photo on file selection
            $('#photo').change(function() {
                readURL(this);
            });

            // Phone number validation
            $('#phone').keyup(function() {
                var phoneNumber = $(this).val();
                var isValid = validatePhoneNumber(phoneNumber);

                if (!isValid) {
                    $('#phone-error').text('Please enter a valid Indian mobile number.');
                } else {
                    $('#phone-error').text('');
                }
            });

            function validatePhoneNumber(phoneNumber) {
                // Regular expression for Indian mobile numbers
                var pattern = /^[6-9]\d{9}$/;

                // Test the phone number against the pattern
                return pattern.test(phoneNumber);
            }

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('#photo-preview').attr('src', e.target.result).show();
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }

            // Form validation
            $('#registration-form').submit(function(e) {
                // Perform your form validation logic here
                var isValid = validateForm();

                if (!isValid) {
                    e.preventDefault();
                }
            });

            function validateForm() {
                // Implement your form validation logic here
                // ...

                // Return true if the form is valid, false otherwise
                // ...
            }

            // Password hint
            $('#password').keyup(function() {
                var password = $(this).val();
                var strength = checkPasswordStrength(password);

                $('#password-hint').text('Password Strength: ' + strength);
            });

            function checkPasswordStrength(password) {
                // Implement your password strength checking logic here
                var strength = 'Weak';

                // Perform the necessary checks and update the strength variable accordingly
                // ...

                return strength;
            }
        });
    </script>
</body>
</html>
