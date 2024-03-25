<?php

?>
<div class="container">
    <div class="row">
        <div>
            <h2 style="color: #4a90e2; font-size: 24px; font-weight: bold;">Post new thread</h2>

            <hr style="background-color: #4a90e2;">
            <?php if (!empty($errors)) : ?>
                <?php foreach ($errors as $error) : ?>
                    <div class="alert alert-info"><?= htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form id="upload-form" action="parent_register" method="post" enctype="multipart/form-data" class="d-flex flex-column align-items-left">

                <div class="mb-3 w-50">
                    <label for="subject" class="form-label">Subject:</label>
                    <input type="text" class="form-control" id="subject" name="subject" maxlength="100">
                </div>

                <div class="mb-3 w-100">
                    <label for="text" class="form-label">Text:</label>
                    <textarea class="form-control" id="text" name="text" rows="3" maxlength="500"></textarea>
                </div>
                <input type="file" id="image-input" name="image" accept="image/*">

                <div class="d-flex justify-content-end w-100">

                    <button type="submit" class="btn btn-primary">Register</button>
                </div>
            </form>
            <div id="preview" class="mt-2"></div>

        </div>

        <div class="col mt-3">



            <h2 style="color: #4a90e2; font-size: 24px; font-weight: bold;">Threads</h2>

        </div>

        <?php if (empty($threads)) : ?>
            <div class="alert alert-info">No thread has been registered yet.</div>
        <?php else : ?>
            <ul class="list-group">
                <?php foreach ($threads as $thread) : ?>

                    <li class="list-group-item d-flex flex-column align-items-left mt-2">
                        <a href="/thread?id=<?= htmlspecialchars($thread->getPost_id()) ?>" class="text-decoration-none text-secondary">
                            <div class="d-flex justify-content-between align-items-left ">

                                <div>
                                    <div class="mb-2">
                                        <h3 style="color: #4a90e2; font-size: 22px; font-weight: bold;">
                                            <?= htmlspecialchars($thread->getSubject()) ?>
                                            <span class="text-secondary px-2" style="font-size: 14px;">
                                                <?= htmlspecialchars($thread->getCreated_at()) ?>
                                            </span>
                                        </h3>
                                    </div>
                                    <p class="px-3"><?= htmlspecialchars($thread->getText()) ?></p>
                                </div>

                                <?php if (!empty($thread->getFileName())) : ?>
                                    <img src="<?= '/img/thumbnail/' . $thread->getFileName()  ?>" alt="Thumbnail" class="img-thumbnail">
                                <?php else : ?>
                                    <div>No img</div>
                                <?php endif; ?>
                            </div>
                        </a>
                        <?php if (!empty($replies[$thread->getPost_id()])) : ?>
                            <h3 style="color: #4a90e2; font-size: 18px; font-weight: bold;">Replies</h3>
                            <ul class="list-group mt-2">
                                <?php foreach ($replies[$thread->getPost_id()] as $reply) : ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center text-decoration-none text-secondary">
                                        <div class=" d-flex flex-column ">

                                            <p><?= htmlspecialchars($reply->getText()) ?></p>
                                            <small><?= htmlspecialchars($reply->getCreated_at()) ?></small>
                                        </div>
                                        <?php if (!empty($reply->getFileName())) : ?>
                                            <img src="<?= '/img/thumbnail/' . $reply->getFileName()  ?>" alt="Thumbnail" class="img-thumbnail">
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    </div>
</div>
</div>

<script>
    document.getElementById('image-input').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Image preview">';
            };
            reader.readAsDataURL(file);
        }
    });

    document.getElementById('upload-form').addEventListener('submit', function(event) {
        event.preventDefault(); // まずフォームの送信を停止

        const fileInput = document.getElementById('image-input');
        const file = fileInput.files[0];

        // ファイルがアップロードされているか確認
        if (!file) {
            alert('ファイルが選択されていません。');
            return;
        }

        // ファイルの形式を確認
        const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!validImageTypes.includes(file.type)) {
            alert('JPEG、PNG、またはGIF形式の画像を選択してください。');
            return;
        }

        // すべてのチェックが通った場合、フォームを送信
        this.submit();
    });
</script>