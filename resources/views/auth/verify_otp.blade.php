<form method="POST" action="{{ route('otp.verify') }}">
    @csrf
    <label>Enter OTP:</label>
    <input type="text" name="otp" required>
    <button type="submit">Verify</button>
</form>
