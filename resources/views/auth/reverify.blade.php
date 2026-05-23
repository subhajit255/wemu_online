<!DOCTYPE html>
<html lang="en">
<head>
    <title>WEMU - Identity Re-verification</title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('assets/css/style.bundle.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/cdn/toastr.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('assets/js/custom_js/cdn/jquery.min.js') }}"></script>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #060608;
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 12% 20%, rgba(76,29,149,0.42) 0%, transparent 45%),
                radial-gradient(circle at 88% 80%, rgba(225,29,72,0.36) 0%, transparent 45%),
                radial-gradient(circle at 55% 50%, rgba(37,99,235,0.28) 0%, transparent 50%);
            background-color: #060608;
            animation: bgBreathe 16s ease-in-out infinite alternate;
        }
        @keyframes bgBreathe {
            0%   { opacity: 0.85; transform: scale(1); }
            100% { opacity: 1;    transform: scale(1.07); }
        }
        .auth-card {
            background: rgba(255,255,255,0.022);
            backdrop-filter: blur(40px);
            -webkit-backdrop-filter: blur(40px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 22px;
            padding: 40px 35px;
            width: 100%;
            max-width: 650px;
            position: relative;
            z-index: 10;
        }
        .step-heading {
            font-size: 26px;
            font-weight: 800;
            margin: 0 0 10px;
            letter-spacing: 0.5px;
            text-align: center;
        }
        .step-sub {
            font-size: 14px;
            color: rgba(255,255,255,0.6);
            line-height: 1.5;
            margin-bottom: 24px;
            text-align: center;
        }
        .rejection-alert {
            background: rgba(225,29,72,0.15);
            border: 1px solid rgba(225,29,72,0.3);
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }
        .rejection-alert i {
            color: #fb7185;
            font-size: 24px;
            margin-top: 2px;
        }
        .rejection-alert .title {
            color: #fff;
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 15px;
        }
        .rejection-alert .reason {
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            line-height: 1.5;
        }
        
        /* Dropzone styles from register page */
        .dropzone {
            border: 2px dashed rgba(255,255,255,0.15);
            border-radius: 14px;
            background: rgba(255,255,255,0.02);
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
        }
        .dropzone:hover {
            border-color: rgba(168,85,247,0.5);
            background: rgba(168,85,247,0.05);
        }
        .dropzone.has-preview {
            border-style: solid;
            border-color: rgba(168,85,247,0.4);
        }
        .dropzone input[type="file"] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 5;
        }
        .dropzone-rect {
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .dz-inner {
            text-align: center;
            transition: opacity 0.3s;
            pointer-events: none;
        }
        .dz-icon {
            font-size: 32px;
            color: rgba(255,255,255,0.3);
            margin-bottom: 10px;
        }
        .dz-label {
            font-size: 14px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 4px;
        }
        .dz-hint {
            font-size: 12px;
            color: rgba(255,255,255,0.4);
        }
        .dz-preview {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
            display: none;
        }
        .dz-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(2px);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            opacity: 0;
            transition: opacity 0.2s;
            pointer-events: none;
        }
        .dropzone:hover .dz-overlay { opacity: 1; }
        .req { color: #f43f5e; }
        
        .btn-wemu {
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn-primary-grad {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            background-size: 200% 200%;
            color: #fff;
            width: 100%;
            margin-top: 16px;
        }
        .btn-primary-grad:hover {
            box-shadow: 0 8px 25px rgba(168,85,247,0.4);
            transform: translateY(-2px);
            color: #fff;
        }
        .btn-primary-grad.loading {
            pointer-events: none;
            opacity: 0.8;
        }
        .btn-primary-grad .btn-spinner { display: none; }
        .btn-primary-grad.loading .btn-label { display: none; }
        .btn-primary-grad.loading .btn-spinner { display: inline-flex; align-items: center; gap: 8px; }
        
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: rgba(255,255,255,0.5);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.2s;
        }
        .logout-link:hover { color: #fff; }
    </style>
</head>
<body>
    <div class="bg-mesh"></div>
    <div class="auth-card">
        <h2 class="step-heading">Identity Re-verification</h2>
        <p class="step-sub">Your previous submission was rejected. Please review the feedback and upload new documents.</p>

        <div class="rejection-alert">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <div class="title">Verification Rejected</div>
                <div class="reason">{{ $verification->rejection_reason ?? 'Please provide clearer images of your ID and selfie.' }}</div>
            </div>
        </div>

        <form id="reverifyForm" action="{{ route('artist.reverify.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="field-label" style="display:block;margin-bottom:10px;">
                        Government ID — Front <span class="req">*</span>
                    </label>
                    <div class="dropzone dropzone-rect" id="dzIdFront">
                        <input type="file" name="government_id_front" accept="image/*" onchange="dzPreview(this,'prevIdFront','dzIdFront')">
                        <div class="dz-inner">
                            <i class="fas fa-id-card dz-icon"></i>
                            <div class="dz-label">Upload Front</div>
                            <div class="dz-hint">Clear, unobstructed photo</div>
                        </div>
                        <img id="prevIdFront" alt="" class="dz-preview"
                            @if(isset($verification) && $verification->government_id_front)
                                src="{{ asset('storage/verification/'.$verification->government_id_front) }}"
                            @endif>
                        <div class="dz-overlay"><i class="fas fa-pencil-alt" style="font-size:18px;"></i><span>Change</span></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="field-label" style="display:block;margin-bottom:10px;">
                        Government ID — Back <span class="req">*</span>
                    </label>
                    <div class="dropzone dropzone-rect" id="dzIdBack">
                        <input type="file" name="government_id_back" accept="image/*" onchange="dzPreview(this,'prevIdBack','dzIdBack')">
                        <div class="dz-inner">
                            <i class="fas fa-id-card-alt dz-icon"></i>
                            <div class="dz-label">Upload Back</div>
                            <div class="dz-hint">Clear, unobstructed photo</div>
                        </div>
                        <img id="prevIdBack" alt="" class="dz-preview"
                            @if(isset($verification) && $verification->government_id_back)
                                src="{{ asset('storage/verification/'.$verification->government_id_back) }}"
                            @endif>
                        <div class="dz-overlay"><i class="fas fa-pencil-alt" style="font-size:18px;"></i><span>Change</span></div>
                    </div>
                </div>
                <div class="col-12">
                    <label class="field-label" style="display:block;margin-bottom:10px;">
                        Selfie Holding ID <span class="req">*</span>
                    </label>
                    <div class="dropzone dropzone-rect" id="dzSelfie">
                        <input type="file" name="selfie_image" accept="image/*" onchange="dzPreview(this,'prevSelfie','dzSelfie')">
                        <div class="dz-inner">
                            <i class="fas fa-portrait dz-icon"></i>
                            <div class="dz-label">Upload Selfie</div>
                            <div class="dz-hint">Face and ID must both be clearly visible</div>
                        </div>
                        <img id="prevSelfie" alt="" class="dz-preview"
                            @if(isset($verification) && $verification->selfie_image)
                                src="{{ asset('storage/verification/'.$verification->selfie_image) }}"
                            @endif>
                        <div class="dz-overlay"><i class="fas fa-pencil-alt" style="font-size:18px;"></i><span>Change</span></div>
                    </div>
                    <p style="text-align:center;font-size:12px;color:rgba(255,255,255,0.35);margin-top:10px;">JPG or PNG only · Max 5MB per file</p>
                </div>
            </div>

            <button type="submit" class="btn-wemu btn-primary-grad" id="btnSubmit">
                <span class="btn-label">Submit Documents</span>
                <span class="btn-spinner"><span class="spinner-border spinner-border-sm"></span> Uploading...</span>
            </button>
        </form>
        
        <a href="{{ route('artist.logout') }}" class="logout-link">Log out instead</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/toastr.js') }}"></script>
    <script>
        function dzPreview(input, previewId, dropzoneId) {
            var f = input.files[0];
            if (!f) return;
            var reader = new FileReader();
            reader.onload = function(e) {
                var $dz     = $('#' + dropzoneId);
                var $prev   = $('#' + previewId);
                var $inner  = $dz.find('.dz-inner');
                $prev.attr('src', e.target.result).css('display', 'block');
                $inner.css({ opacity: 0, pointerEvents: 'none' });
                $dz.addClass('has-preview');
                $dz.css({ borderStyle: 'solid', borderColor: 'rgba(168,85,247,0.4)' });
            };
            reader.readAsDataURL(f);
        }

        $(document).ready(function() {
            $('.dz-preview').each(function() {
                var src = $(this).attr('src');
                if (src && src.length > 0) {
                    var $dz = $(this).closest('.dropzone');
                    $dz.addClass('has-preview');
                    $dz.find('.dz-inner').css({ opacity: 0 });
                    $dz.css({ borderStyle: 'solid', borderColor: 'rgba(168,85,247,0.4)' });
                    $(this).css('display', 'block');
                }
            });

            $('#reverifyForm').on('submit', function(e) {
                e.preventDefault();
                var $btn = $('#btnSubmit');
                $btn.addClass('loading').prop('disabled', true);

                var fd = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: fd,
                    processData: false,
                    contentType: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(resp) {
                        $btn.removeClass('loading').prop('disabled', false);
                        if (resp.status) {
                            toastr.success(resp.message || 'Saved successfully!');
                            setTimeout(function() {
                                window.location.href = resp.url;
                            }, 1000);
                        } else {
                            toastr.error(resp.message || 'Something went wrong.');
                        }
                    },
                    error: function(xhr) {
                        $btn.removeClass('loading').prop('disabled', false);
                        var msg = 'An error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            msg = Object.values(errors)[0][0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            });
        });
    </script>
</body>
</html>
