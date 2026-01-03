<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <style>
        .action-icons a {
            margin: 0 5px;
            text-decoration: none;
            color: #333;
            font-size: 1.2em;
        }

        .action-icons a:hover {
            color: #007bff;
        }

        .action-icons a.delete:hover {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <h1><?= esc($page_title) ?></h1>

    <?php if (session()->getFlashdata('message')): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('message')) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($users)): ?>
        <table id="usersTable" class="display">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Created at</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= esc($user['id']) ?></td>
                        <td><?= esc($user['username']) ?></td>
                        <td><?= esc($user['first_name']) ?></td>
                        <td><?= esc($user['last_name']) ?></td>
                        <td><?= esc($user['email']) ?></td>
                        <td><?= esc($user['created_at']) ?></td>
                        <td class="action-icons">
                            <a href="<?= base_url('user/id/' . $user['id']) ?>" title="View">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="<?= base_url('user/edit/' . $user['id']) ?>" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="<?= base_url('user/delete/' . $user['id']) ?>"
                                method="post"
                                class="delete-form"
                                style="display:inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="DELETE">

                                <button type="submit"
                                    title="Delete"
                                    style="background:none;border:none;padding:0;cursor:pointer;">
                                    <i class="fas fa-trash text-danger"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No users found</p>
    <?php endif; ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                pageLength: 10,
                order: [
                    [0, 'asc']
                ]
            });

            // Delete confirmation for DELETE form (CI4 method spoofing)
            $(document).on('submit', '.delete-form', function(e) {
                e.preventDefault();

                const form = this;
                const row = $(this).closest('tr');

                const userId = row.find('td:eq(0)').text();
                const username = row.find('td:eq(1)').text();
                const firstName = row.find('td:eq(2)').text();
                const lastName = row.find('td:eq(3)').text();

                const confirmMessage =
                    '⚠️ ΕΠΙΒΕΒΑΙΩΣΗ ΔΙΑΓΡΑΦΗΣ\n\n' +
                    'ID: ' + userId + '\n' +
                    'Username: ' + username + '\n' +
                    'Όνομα: ' + firstName + ' ' + lastName + '\n\n' +
                    '⚠️ Αυτή η ενέργεια δεν μπορεί να αναιρεθεί!';

                if (confirm(confirmMessage)) {
                    form.submit();
                }
            });
        });
    </script>
</body>

</html>