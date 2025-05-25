<form method="POST" action="/profile">
    @csrf
    @method('PATCH')
    <input type="text" name="name" value="gregory">
    <input type="email" name="email" value="emegwalio@yahoo.com">
    <button type="submit">Save</button>
</form>
