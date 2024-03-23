<?php
// print_r($threads);
print_r($replies);


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

            <form id="threadForm" action="register" method="post" class="d-flex flex-column align-items-left">
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
        </div>

        <div class="col mt-3">


    
    <h2 style="color: #4a90e2; font-size: 24px; font-weight: bold;">Threads</h2>

</div>

            <?php if (empty($threads)) : ?>
                <div class="alert alert-info">スレッドは登録されていません。</div>
            <?php else : ?>
                <ul class="list-group">
                    <?php foreach ($threads as $thread) : ?>
                        <li class="list-group-item">
                            <a href="/post?id=<?= htmlspecialchars($thread->getPost_id()) ?>" class="text-decoration-none text-secondary">
                                <h5><?= htmlspecialchars($thread->getSubject()) ?></h5>
                                <p> <?= htmlspecialchars($thread->getText()) ?></p><br>
                                <small> <?= htmlspecialchars($thread->getCreated_at()) ?></small><br>

                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>