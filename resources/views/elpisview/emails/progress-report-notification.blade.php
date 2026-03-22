<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Elpis View Educational Services</h1>
        <p>Monthly Progress Report</p>
    </div>

    <div class="content">
        <p>Dear {{ $report->student->guardian->name ?? 'Parent/Guardian' }},</p>

        <p>The monthly progress report for <strong>{{ $report->student->full_name }}</strong>
           in <strong>{{ $report->subject->name }}</strong>
           for <strong>{{ $report->period_label }}</strong> has been prepared and approved.</p>

        <p>Please log in to your parent portal to view the full report.</p>

        <a href="{{ route('elpisview.parent.dashboard') }}" class="btn">View Report in Portal</a>

        <p style="margin-top: 20px;">
            <strong>Report Summary:</strong><br>
            Tutor: {{ $report->tutor->name }}<br>
            Classes Attended: {{ $report->attendance_count }} / {{ $report->total_classes }}<br>
        </p>

        <p>If you have any questions, please contact your regional manager.</p>

        <p>Best regards,<br>Elpis View Educational Services</p>
    </div>

    <div class="footer">
        <p>This is an automated notification from Elpis View Educational Services.</p>
    </div>
</body>
</html>
