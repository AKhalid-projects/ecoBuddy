<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Bootstrap CSS for styling the login form -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <!-- Centering the login form vertically and horizontally -->
    <div class="row justify-content-center">
        <div class="col-md-6">
            <!-- Card for the login form -->
            <div class="card">
                <div class="card-header text-center">
                    <!-- Title of the login card -->
                    <h1 class="h4">Login</h1>
                </div>
                <div class="card-body">
                    <!-- Form for user login -->
                    <form method="POST" action="/ecoBuddy/index.php">
                        <!-- Username Input Field -->
                        <div class="mb-3">
                            <label for="username" class="form-label">Username:</label>
                            <!-- Text input field for username -->
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <!-- Password Input Field -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <!-- Password input field for password -->
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <!-- Login Submit Button -->
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="text-center mt-3">
                <p>Don’t have an account? Contact the administrator.</p> <!-- Message for users who need an account -->
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS for interactivity and validation -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
