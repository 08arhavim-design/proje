<?php
function fmt_date_tr($d){ return $d ? date('d.m.Y', strtotime($d)) : '-'; }
function fmt_time($t){ return $t ? substr((string)$t,0,5) : ''; }

function lines_to_ul($text){
  $text = trim((string)$text);
  if ($text === '') return '';
  $lines = preg_split("/\r\n|\n|\r/", $text);
  $out = '<ul style="margin:8px 0 0 16px; padding:0; color:#c9d1d9; font-size:12px; line-height:1.5;">';
  foreach($lines as $ln){
    $ln = trim($ln);
    if ($ln === '') continue;
    $ln = ltrim($ln, "-•\t ");
    $out .= '<li>'.e($ln).'</li>';
  }
  return $out.'</ul>';
}

$grouped = [];
foreach(($upcoming ?? []) as $e){
  $k = (string)$e['event_date'];
  $grouped[$k][] = $e;
}
?>
<style>
  .box{max-width:980px;margin:0 auto;}
  .panel{background:#0d1117;border:1px solid rgba(255,255,255,0.08);border-radius:16px;padding:16px;margin-top:14px;}
  .h{margin:0 0 10px 0;font-size:16px;border-left:4px solid #f39c12;padding-left:12px}
  .day{margin-top:14px;background:#161b22;border:1px solid #30363d;border-radius:16px;padding:14px;}
  .dayTitle{display:flex;gap:10px;align-items:center;justify-content:space-between;flex-wrap:wrap;}
  .dayBadge{font-weight:900;color:#0d1117;background:#f39c12;border-radius:12px;padding:8px 12px;}
  .festival{color:#8b949e;font-size:12px;font-weight:800;}
  .item{margin-top:10px;background:#0b0e14;border:1px solid rgba(255,255,255,0.06);border-radius:14px;padding:12px;display:flex;gap:12px;justify-content:space-between;align-items:flex-start;}
  .t{font-weight:900;color:#fff}
  .m{color:#8b949e;font-size:12px;line-height:1.4}
  .time{white-space:nowrap;font-weight:900;color:#58a6ff;border:1px solid rgba(88,166,255,0.25);background:rgba(88,166,255,0.12);padding:6px 10px;border-radius:999px;}
  .award{display:inline-block;margin-top:8px;font-size:12px;font-weight:900;color:#2ecc71;border:1px solid rgba(35,134,54,0.35);background:rgba(35,134,54,0.16);padding:6px 10px;border-radius:999px;}
  @media(max-width:520px){.item{flex-direction:column}.time{width:max-content}}
</style>

<div class="box">
  <div class="panel">
    <h2 class="h">📅 Boğa Güreşleri Takvimi</h2>

    <?php if (empty($grouped)): ?>
      <div class="m">Yaklaşan etkinlik bulunmuyor.</div>
    <?php else: ?>
      <?php foreach($grouped as $date => $items): ?>
        <div class="day">
          <div class="dayTitle">
            <div>
              <div class="festival"><?= e($items[0]['festival'] ?? '') ?></div>
              <div style="font-weight:900;font-size:14px;"><?= e(fmt_date_tr($date)) ?></div>
            </div>
            <div class="dayBadge"><?= e(fmt_date_tr($date)) ?></div>
          </div>

          <?php foreach($items as $e): ?>
            <div class="item">
              <div>
                <div class="t"><?= e($e['title'] ?? '') ?></div>
                <div class="m">
                  <?= e(trim(($e['city'] ?? '').' '.(($e['district'] ?? '') ? '/ '.$e['district'] : ''))) ?>
                  <?= !empty($e['location']) ? ' • '.e($e['location']) : '' ?>
                </div>

                <?= lines_to_ul($e['categories'] ?? '') ?>

                <?php if(!empty($e['has_award'])): ?>
                  <div class="award">Ödül Töreni</div>
                <?php endif; ?>

                <?php if(!empty($e['notes'])): ?>
                  <div class="m" style="margin-top:8px;"><?= nl2br(e($e['notes'])) ?></div>
                <?php endif; ?>
              </div>

              <div class="time"><?= $e['start_time'] ? e(fmt_time($e['start_time'])) : 'Saat yok' ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
