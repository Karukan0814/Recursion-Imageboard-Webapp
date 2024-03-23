<div class="col-12">
    <form action="#" method="post" id="update-part-form" class="d-flex row">
        <?php if ($part?->getId() !== null): ?>
            <input type="hidden" name="id" value="<?= $part->getId() ?>" placeholder="ID"><br>
        <?php endif; ?>
        <input type="text" name="name" value="<?= $part? htmlspecialchars($part->getName()) : '' ?>" placeholder="Name" required><br>
        <input type="text" name="type" value="<?= $part? htmlspecialchars($part->getType()) : '' ?>" placeholder="Type" required><br>
        <input type="text" name="brand" value="<?= $part? htmlspecialchars($part->getBrand()) : '' ?>" placeholder="Brand" required><br>
        <input type="text" name="modelNumber" value="<?= $part? htmlspecialchars($part->getModelNumber()) : '' ?>" placeholder="Model Number" required><br>
        <input type="text" name="releaseDate" value="<?= $part? htmlspecialchars($part->getReleaseDate()) : '' ?>" placeholder="Release Date (YYYY-MM-DD)" required><br>
        <textarea name="description" placeholder="Description" required><?= $part? htmlspecialchars($part->getDescription()) : '' ?></textarea><br>
        <input type="number" name="performanceScore" value="<?= $part? $part->getPerformanceScore() : '' ?>" placeholder="Performance Score" required><br>
        <input type="number" name="marketPrice" value="<?= $part? $part->getMarketPrice() : '' ?>" placeholder="Market Price" required><br>
        <input type="number" name="rsm" value="<?= $part? $part->getRsm() : '' ?>" placeholder="RSM" required><br>
        <input type="number" name="powerConsumptionW" value="<?= $part? $part->getPowerConsumptionW() : '' ?>" placeholder="Power Consumption (W)" required><br>
        <label>Dimensions (L x W x H):</label><br>
        <input type="number" step="0.01" name="lengthM" value="<?= $part ? $part->getLengthM() : '' ?>" placeholder="Length (meters)" required>
        <input type="number" step="0.01" name="widthM" value="<?= $part ? $part->getWidthM() : '' ?>" placeholder="Width (meters)" required>
        <input type="number" step="0.01" name="heightM" value="<?= $part ? $part->getHeightM() : '' ?>" placeholder="Height (meters)" required><br>
        <input type="number" name="lifespan" value="<?= $part? $part->getLifespan() : '' ?>" placeholder="Lifespan (years)" required><br>
        <input type="submit" value="Update Part">
    </form>
</div>

<script src="/js/app.js"></script>