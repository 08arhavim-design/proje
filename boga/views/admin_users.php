<?php
// $users, $error, $ok, $csrf, $isSuper, $allowedRoles
?>

<style>
  .wrap{background:#0d1117;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
  .msg{margin:10px 0;padding:10px 12px;border-radius:12px}
  .msg.err{background:rgba(218,54,51,.15);border:1px solid rgba(218,54,51,.35)}
  .msg.ok{background:rgba(35,134,54,.15);border:1px solid rgba(35,134,54,.35)}
  table{width:100%;border-collapse:collapse;margin-top:12px}
  th,td{padding:10px 8px;border-bottom:1px solid rgba(255,255,255,.08);text-align:left}
  th{color:#8b949e;font-size:12px}
  td{color:#fff;font-size:13px}
  .pill{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:900;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06)}
  .pill.user{color:#8b949e}
  .pill.admin{color:#f39c12;border-color:rgba(243,156,18,.35);background:rgba(243,156,18,.12)}
  .pill.superadmin{color:#2ecc71;border-color:rgba(46,204,113,.35);background:rgba(46,204,113,.10)}
  .pill.active{color:#2ecc71}
  .pill.passive{color:#ff7b72;border-color:rgba(218,54,51,.35);background:rgba(218,54,51,.10)}
  select{padding:8px 10px;border-radius:10px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);color:#fff}
  button{padding:8px 10px;border-radius:10px;border:1px solid rgba(255,255,255,.14);background:rgba(243,156,18,.14);color:#f39c12;font-weight:900;cursor:pointer}
  .muted{color:#8b949e;font-size:12px}
  @media (max-width: 900px){
    table, thead, tbody, th, td, tr { display:block; }
    thead { display:none; }
    tr{border:1px solid rgba(255,255,255,.08);border-radius:14px;margin:10px 0;padding:10px;background:rgba(255,255,255,.03)}
    td{border:0;padding:6px 0}
    td::before{content:attr(data-label);display:block;color:#8b949e;font-size:11px;margin-bottom:2px}
  }
</style>

<div class="wrap">
  <div style="font-size:18px;font-weight:900;">Kullanıcılar</div>
  <div class="muted">Superadmin rolündeyseniz kullanıcı rollerini buradan değiştirebilirsiniz.</div>

  <?php if (!empty($error)): ?>
    <div class="msg err"><?php echo e($error); ?></div>
  <?php endif; ?>
  <?php if (!empty($ok)): ?>
    <div class="msg ok"><?php echo e($ok); ?></div>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>Kullanıcı</th>
        <th>Telefon</th>
        <th>Rol</th>
        <th>Durum</th>
        <th>Tarih</th>
        <?php if (!empty($isSuper)): ?><th>İşlem</th><?php endif; ?>
      </tr>
    </thead>
    <tbody>
    <?php foreach (($users ?? []) as $u): ?>
      <tr>
        <td data-label="Kullanıcı"><?php echo e($u['username'] ?? ''); ?></td>
        <td data-label="Telefon"><?php echo e($u['phone'] ?? '-'); ?></td>

        <td data-label="Rol">
          <?php
            $r = (string)($u['role'] ?? 'user');
            $cls = ($r === 'superadmin') ? 'superadmin' : (($r === 'admin') ? 'admin' : 'user');
          ?>
          <span class="pill <?php echo $cls; ?>"><?php echo e($r); ?></span>
        </td>

        <td data-label="Durum">
          <?php
            $st = (string)($u['status'] ?? 'active');
            $stCls = ($st === 'active' || $st === 'Aktif') ? 'active' : 'passive';
          ?>
          <span class="pill <?php echo $stCls; ?>"><?php echo e($st ?: 'Aktif'); ?></span>
        </td>

        <td data-label="Tarih">
          <?php
            $dt = (string)($u['created_at'] ?? '');
            echo e(($dt && $dt !== '0000-00-00 00:00:00') ? substr($dt,0,10) : '-');
          ?>
        </td>

        <?php if (!empty($isSuper)): ?>
          <td data-label="İşlem">
            <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
              <input type="hidden" name="csrf" value="<?php echo e($csrf ?? ''); ?>">
              <input type="hidden" name="action" value="update_role">
              <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">

              <select name="role">
                <?php foreach (($allowedRoles ?? ['user','admin','superadmin']) as $roleOpt): ?>
                  <option value="<?php echo e($roleOpt); ?>" <?php echo ((string)$u['role'] === (string)$roleOpt) ? 'selected' : ''; ?>>
                    <?php echo e($roleOpt); ?>
                  </option>
                <?php endforeach; ?>
              </select>

              <button type="submit">Kaydet</button>
            </form>

            <?php if ((int)$u['id'] === current_user_id()): ?>
              <div class="muted" style="margin-top:6px;">Not: Kendi rolünüzü düşürmeniz engellidir.</div>
            <?php endif; ?>
          </td>
        <?php endif; ?>

      </tr>
    <?php endforeach; ?>

    <?php if (empty($users)): ?>
      <tr><td colspan="6" class="muted">Kayıt bulunamadı.</td></tr>
    <?php endif; ?>
    </tbody>
  </table>
</div>
