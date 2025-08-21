<p>Dear {{ $user->name }},</p>

@if($status)
    <p>We would like to inform you that your account was activated by the administrator.</p>
    <p>You can now access the <i>File Manager App</i>.</p>
@else
    <p>We would like to inform you that your account was deactivated by the administrator.</p>
    <p><b>You will not be able</b> to access the <i>File Manager App</i> until further notice.</p>
@endif

<p>Sincerely,</p>
<p><i>The File Manager App Team.</i></p>
