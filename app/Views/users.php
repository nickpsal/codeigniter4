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
                            <a href="<?= base_url('user/id/' . $user['id']) ?>" title="View"><i class="fas fa-eye"></i></a>
                            <a href="<?= base_url('user/edit/' . $user['id']) ?>" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="<?= base_url('user/delete/' . $user['id']) ?>" class="delete" title="Delete"><i class="fas fa-trash"></i></a>
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
        $(document).ready(function () {
            $('#usersTable').DataTable({
                pageLength: 10,
                order: [[0, 'asc']]
            });

            // Delete confirmation with event delegation (works with DataTables)
            $(document).on('click', '.action-icons a.delete', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).attr('href');
                const row = $(this).closest('tr');
                const username = row.find('td:eq(1)').text(); // Get username from second column
                const firstName = row.find('td:eq(2)').text();
                const lastName = row.find('td:eq(3)').text();
                const userId = row.find('td:eq(0)').text(); // Get ID from first column
                
                const confirmMessage = '⚠️ ΕΠΙΒΕΒΑΙΩΣΗ ΔΙΑΓΡΑΦΗΣ\n\n' +
                    'Είστε σίγουροι ότι θέλετε να διαγράψετε τον χρήστη;\n\n' +
                    'ID: ' + userId + '\n' +
                    'Username: ' + username + '\n' +
                    'Όνομα: ' + firstName + ' ' + lastName + '\n\n' +
                    '⚠️ Αυτή η ενέργεια δεν μπορεί να αναιρεθεί!';
                
                if (confirm(confirmMessage)) {
                    window.location.href = deleteUrl;
                }
            });
        });
    </script>
</body>
</html>
