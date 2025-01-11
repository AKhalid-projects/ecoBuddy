<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Sets character encoding for the page -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Makes the page responsive on mobile devices -->
    <title>404 - Page Not Found</title> <!-- Title of the page shown in the browser tab -->

    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Container for centering the 404 message -->
<div class="container text-center mt-5">
    <div class="row justify-content-center"> <!-- Centers the content horizontally -->
        <div class="col-md-6"> <!-- Limits the width to 50% of the viewport on medium screens and above -->
            <div class="card shadow-lg"> <!-- Card component with shadow for an elevated look -->
                <div class="card-body"> <!-- Main content of the card -->
                    <h1 class="display-4 text-danger">404</h1> <!-- Large text displaying the "404" error in red -->
                    <p class="lead">Oops! The page you are looking for does not exist.</p> <!-- Subtitle providing information about the error -->
                    <hr> <!-- Horizontal line separator -->
                    <p class="text-muted">It seems you've hit a broken link or entered an incorrect URL.</p> <!-- Additional info with muted (faded) text -->

                    <!-- Button to navigate back to the homepage -->
                    <a href="/ecoBuddy/" class="btn btn-primary mt-3">
                        <i class="fas fa-home"></i> <!-- Home icon -->
                        Go to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JavaScript for interactivity -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- FontAwesome for displaying icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
