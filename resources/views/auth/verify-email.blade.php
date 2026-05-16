<!DOCTYPE html>
<html lang="en">

<head>
    <title>Email Verification | Warranty Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-position-center">

    @if ($emailVerified)
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header text-center bg-success text-white">
                            <h4>Email Verification Successful</h4>
                        </div>
                        <div class="card-body text-center">
                            <p class="mb-4">Thank you for verifying your email address. Your account is now active.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header text-center bg-danger text-white">
                            <h4>Email Verification Failed</h4>
                        </div>
                        <div class="card-body text-center">
                            <p class="mb-4">We could not verify your email address. Please try again or contact
                                support.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
</body>

</html>
