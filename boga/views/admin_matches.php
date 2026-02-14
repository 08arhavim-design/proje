<form method="get" action="">
  <label>Tarih (YYYY-AA-GG)</label>
  <input name="date" value="<?= e($_GET['date'] ?? '') ?>" placeholder="2026-01-24">
  <button class="btn" type="submit">Listele</button>
  <a class="btn btn2" href="<?= e(base_url('/admin/')) ?>">Geri</a>
</form>
<table style="margin-top:12px">
  <thead><tr><th>Tarih</th><th>Meydan</th><th>Boğa</th><th>Rakip</th><th>Sonuç</th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td><?= e($r['match_date']) ?></td>
      <td><?= e($r['arena_name'] ?? '-') ?></td>
      <td><?= e($r['bull_name'] ?? '-') ?></td>
      <td><?= e($r['opponent_text'] ?? '-') ?></td>
      <td><?= e($r['result_text'] ?? '-') ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
