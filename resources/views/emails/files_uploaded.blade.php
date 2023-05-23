<!DOCTYPE html>
<html>

<head>
    <style>
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.5;
            color: #333333;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <h1>Files Uploaded</h1>

        <p>Hi {{ $username }},</p>

        <p>We have uploaded files for you.</p>

        <p>Please click the links below to download the files:</p>

        {!! $downloadLinks !!}
    </div>
</body>

</html>
