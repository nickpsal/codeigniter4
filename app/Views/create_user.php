<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }

        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-header h1 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .form-header p {
            color: #666;
            font-size: 0.95em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group label .required {
            color: #dc3545;
            margin-left: 3px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group input::placeholder {
            color: #999;
        }

        .form-group .help-text {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .error-message {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
            display: none;
        }

        .form-group.error input {
            border-color: #dc3545;
        }

        .form-group.error .error-message {
            display: block;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-container {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h1><i class="fas fa-user-plus"></i> <?= $page_title ?></h1>
            <p>Συμπληρώστε τα παρακάτω στοιχεία</p>
        </div>

        <?php if (isset($errors) && !empty($errors)): ?>
            <div class="error-message" style="display: block; background-color: #f8d7da; color: #721c24; padding: 12px 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                <strong><i class="fas fa-exclamation-triangle"></i> Σφάλματα:</strong>
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <?php foreach ($errors as $key => $error): ?>
                        <li>
                            <?php if (is_numeric($key)): ?>
                                <?= esc($error) ?>
                            <?php else: ?>
                                <strong><?= esc($key) ?>:</strong> <?= esc($error) ?>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($message) && $message === 'User created successfully'): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Ο χρήστης δημιουργήθηκε επιτυχώς!
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= base_url('create_user') ?>" id="createUserForm">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Όνομα Χρήστη <span class="required">*</span>
                </label>
                <input 
                    type="text"     
                    id="username" 
                    name="username" 
                    placeholder="Εισάγετε όνομα χρήστη"
                    value="<?= old('username') ?>"
                    required
                    minlength="3"
                    maxlength="100"
                >
                <div class="help-text">Μέγιστο 100 χαρακτήρες, πρέπει να είναι μοναδικό</div>
                <div class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email <span class="required">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="example@email.com"
                    value="<?= old('email') ?>"
                    required
                    maxlength="150"
                >
                <div class="help-text">Μέγιστο 150 χαρακτήρες, πρέπει να είναι μοναδικό</div>
                <div class="error-message"></div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">
                        <i class="fas fa-id-card"></i> Όνομα <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="first_name" 
                        name="first_name" 
                        placeholder="Όνομα"
                        value="<?= old('first_name') ?>"
                        required
                        maxlength="100"
                    >
                    <div class="error-message"></div>
                </div>

                <div class="form-group">
                    <label for="last_name">
                        <i class="fas fa-id-card"></i> Επώνυμο <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="last_name" 
                        name="last_name" 
                        placeholder="Επώνυμο"
                        value="<?= old('last_name') ?>"
                        required
                        maxlength="100"
                    >
                    <div class="error-message"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Κωδικός Πρόσβασης <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Εισάγετε κωδικό πρόσβασης"
                    required
                    minlength="8"
                >
                <div class="help-text">Ελάχιστο 8 χαρακτήρες</div>
                <div class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="password_confirm">
                    <i class="fas fa-lock"></i> Επιβεβαίωση Κωδικού <span class="required">*</span>
                </label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    placeholder="Επαναλάβετε τον κωδικό πρόσβασης"
                    required
                    minlength="8"
                >
                <div class="error-message"></div>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Αποθήκευση
                </button>
                <a href="<?= base_url('users') ?>" class="btn btn-secondary" style="text-decoration: none;">
                    <i class="fas fa-times"></i> Ακύρωση
                </a>
            </div>
        </form>
    </div>

    <script>
        // Client-side validation
        document.getElementById('createUserForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirm').value;

            if (password !== passwordConfirm) {
                e.preventDefault();
                const confirmField = document.getElementById('password_confirm').closest('.form-group');
                confirmField.classList.add('error');
                const errorMsg = confirmField.querySelector('.error-message');
                errorMsg.textContent = 'Οι κωδικοί πρόσβασης δεν ταιριάζουν';
                return false;
            }
        });

        // Real-time password confirmation check
        document.getElementById('password_confirm').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const passwordConfirm = this.value;
            const formGroup = this.closest('.form-group');

            if (passwordConfirm && password !== passwordConfirm) {
                formGroup.classList.add('error');
                formGroup.querySelector('.error-message').textContent = 'Οι κωδικοί πρόσβασης δεν ταιριάζουν';
            } else {
                formGroup.classList.remove('error');
                formGroup.querySelector('.error-message').textContent = '';
            }
        });
    </script>
</body>
</html>

