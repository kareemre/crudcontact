
<form method="POST" action="update.php">

    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="">
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="">
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone" value="">
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>