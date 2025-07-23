
    $(document).ready(function () {
        $("form").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    email: true
                },
                dob: {
                    required: true,
                    date: true
                },
                gender: {
                    required: true
                },
                address: {
                    required: true
                },
                departmentname: {
                    required: true
                },
                city: {
                    required: true
                },
                postal_code: {
                    required: true,
                    digits: true,
                    minlength: 5,
                    maxlength: 6
                },
                CV: {
                    required: true,
                    extension: "pdf|doc|docx"
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 15
                },
                Avatar: {
                    required: true,
                    extension: "jpg|jpeg|png"
                },
                Password: {
                    required: true,
                    minlength: 8
                },
                "Confirm Password": {
                    required: true,
                    equalTo: "[name='Password']"
                },
                Bio: {
                    required: true,
                    minlength: 10
                }
            },
            messages: {
                username: {
                    required: "Please enter a username",
                    minlength: "Username must be at least 3 characters"
                },
                email: {
                    required: "Please enter your email",
                    email: "Enter a valid email address"
                },
                dob: {
                    required: "Please enter your date of birth",
                    date: "Enter a valid date"
                },
                gender: {
                    required: "Please select your gender"
                },
                address: {
                    required: "Please enter your address"
                },
                departmentname: {
                    required: "Please select a department"
                },
                city: {
                    required: "Please enter your city"
                },
                postal_code: {
                    required: "Please enter your postal code",
                    digits: "Only numbers allowed",
                    minlength: "Postal code must be at least 5 digits",
                    maxlength: "Postal code cannot exceed 6 digits"
                },
                CV: {
                    required: "Please upload your CV",
                    extension: "Only PDF, DOC, and DOCX files are allowed"
                },
                phone: {
                    required: "Please enter your phone number",
                    digits: "Only numbers allowed",
                    minlength: "Phone number must be at least 10 digits",
                    maxlength: "Phone number cannot exceed 15 digits"
                },
                Avatar: {
                    required: "Please upload your image",
                    extension: "Only JPG, JPEG, and PNG files are allowed"
                },
                Password: {
                    required: "Please enter your password",
                    minlength: "Password must be at least 8 characters long"
                },
                "Confirm Password": {
                    required: "Please confirm your password",
                    equalTo: "Passwords do not match"
                },
                Bio: {
                    required: "Please enter a short biography",
                    minlength: "Biography must be at least 10 characters long"
                }
            },
            errorPlacement: function (error, element) {
                error.css("color", "red").insertAfter(element);
            }
        });
    });
