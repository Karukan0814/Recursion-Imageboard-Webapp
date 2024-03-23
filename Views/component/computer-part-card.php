<div class="card" style="width: 18rem;">
    <div class="card-body">
        <h5 class="card-title"><?= htmlspecialchars($part->getName()) ?></h5>
        <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($part->getType()) ?> - <?= htmlspecialchars($part->getBrand()) ?></h6>
        <p class="card-text">
            <strong>Model:</strong> <?= htmlspecialchars($part->getModelNumber()) ?><br />
            <strong>Release Date:</strong> <?= htmlspecialchars($part->getReleaseDate()) ?><br />
            <strong>Description:</strong> <?= htmlspecialchars($part->getDescription()) ?><br />
            <strong>Performance Score:</strong> <?= $part->getPerformanceScore() ?><br />
            <strong>Market Price:</strong> $<?= $part->getMarketPrice() ?><br />
            <strong>RSM:</strong> $<?= $part->getRsm() ?><br />
            <strong>Power Consumption:</strong> <?= $part->getPowerConsumptionW() ?>W<br />
            <strong>Dimensions:</strong> <?= $part->getLengthM() ?>m x <?= $part->getWidthM() ?>m x <?= $part->getHeightM() ?>m<br />
            <strong>Lifespan:</strong> <?= $part->getLifespan() ?> years<br />
        </p>
        <p class="card-text"><small class="text-muted">Last updated on <?= htmlspecialchars($part->getTimeStamp()?->getUpdatedAt()??'') ?></small></p> <!-- updated_atのゲッターがあると仮定した場合 -->
    </div>
</div>
