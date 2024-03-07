<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contacts as $contact) : ?>
            <tr>
                <td><?= $contact->name; ?></td>
                <td><?= $contact->email; ?></td>
                <td><?= $contact->phone_number; ?></td>
                <td>
                    <a href="update.php?id=<?= $contact->id; ?>" class="btn btn-primary btn-sm">Edit</a>
                    <a href="delete.php?id=<?= $contact->id; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>