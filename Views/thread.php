<?php
// print_r($thread);


?>
<div class="container">
    <div class="row">
        <div class="d-flex flex-column align-items-left">

            <?php if (!empty($errors)) : ?>
                <?php foreach ($errors as $error) : ?>
                    <div class="alert alert-info"><?= htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="d-flex justify-content-between">
                <div class="d-flex flex-column justify-content-between">
                    <div>
                        <div class="mb-3 w-50">
                            <h5 class="fw-bold"><?= htmlspecialchars($thread->getSubject()) ?></h5>
                        </div>

                        <div class="mb-3 w-100">
                            <p><?= htmlspecialchars($thread->getText()) ?></p>
                        </div>
                    </div>
                    <div>


                        <small class="text-muted"><?= htmlspecialchars($thread->getCreated_at()) ?></small>
                    </div>
                </div>
                <?php if (!empty($thread->getFileName())) : ?>
                    <div class="mt-3">
                        <a href="<?= '/img/original/' . $thread->getFileName() ?>" class="text-decoration-none text-secondary">
                            <img src="<?= '/img/thumbnail/' . $thread->getFileName() ?>" alt="Thumbnail" class="img-thumbnail">
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
        <hr style="background-color: #4a90e2;">

        <form id="upload-form" action="child_register" method="post" enctype="multipart/form-data" class="d-flex flex-column align-items-left">

        <input type="hidden" name="reply_id" value="<?= htmlspecialchars($thread->getPost_id()) ?>">

            <div class="mb-3 w-100">
                <label for="text" class="form-label">Reply:</label>
                <textarea class="form-control" id="text" name="text" rows="3" maxlength="500"></textarea>
            </div>
            <input type="file" id="image-input" name="image" accept="image/*">

            <div class="d-flex justify-content-end w-100">

                <button type="submit" class="btn btn-primary">Reply</button>
            </div>
        </form>

        <div class="col mt-3">



            <h2 style="color: #4a90e2; font-size: 24px; font-weight: bold;">Replies</h2>

        </div>

        <?php if (empty($replies)) : ?>
            <div class="alert alert-info">No replies yet.</div>
        <?php else : ?>
            <ul class="list-group">
                <?php foreach ($replies as $reply) : ?>

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="/thread?id=<?= htmlspecialchars($thread->getPost_id()) ?>" class="text-decoration-none text-secondary">
                            <p> <?= htmlspecialchars($reply->getText()) ?></p>
                            <small> <?= htmlspecialchars($reply->getCreated_at()) ?></small>
                        </a>

                        <?php if (!empty($reply->getFileName())) : ?>
                            <img src="<?= '/img/thumbnail/' . $reply->getFileName()  ?>" alt="Thumbnail" class="img-thumbnail">
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
        if (file) {
            
            // ファイルの形式を確認
            const validImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validImageTypes.includes(file.type)) {
                alert('JPEG、PNG、またはGIF形式の画像を選択してください。');
                return;
            }
        }

        // すべてのチェックが通った場合、フォームを送信
        this.submit();
    });
</script>