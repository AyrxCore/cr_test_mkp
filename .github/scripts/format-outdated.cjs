const fs = require('fs');

// --- Utilitaire : parse une version semver et détermine le type de bump ---
function parseVersion(v) {
  if (!v) return null;
  const cleaned = v.replace(/^v/, '').split('-')[0].split('+')[0];
  const parts = cleaned.split('.').map(n => parseInt(n, 10));
  if (parts.some(isNaN)) return null;
  return { major: parts[0] || 0, minor: parts[1] || 0, patch: parts[2] || 0 };
}

function bumpType(current, latest) {
  const c = parseVersion(current);
  const l = parseVersion(latest);
  if (!c || !l) return 'nonClassee';
  if (l.major > c.major) return 'majeure';
  if (l.minor > c.minor) return 'mineure';
  if (l.patch > c.patch) return 'patch';
  return 'nonClassee'; // pas de diff détectée mais listé par outdated : par prudence, on la classe ici plutôt que de la perdre
}

function buildTables(items, columns) {
  const groups = { majeure: [], mineure: [], patch: [], nonClassee: [] };
  for (const item of items) {
    groups[bumpType(item.current, item.latest)].push(item);
  }

  const labels = {
    majeure: '⚠️ Majeures',
    mineure: '🔧 Mineures',
    patch: '🩹 Patch',
  };

  let out = '';
  for (const key of ['majeure', 'mineure', 'patch']) {
    const rows = groups[key];
    out += `\n### ${labels[key]} (${rows.length})\n\n`;
    if (rows.length === 0) {
      out += '_Aucun package._\n';
      continue;
    }
    out += `| ${columns.join(' | ')} |\n`;
    out += `|${columns.map(() => '---').join('|')}|\n`;
    for (const r of rows) {
      out += `| ${r.name} | ${r.current} | ${r.latest} |\n`;
    }
  }

  // Catégorie "non classées" : affichée uniquement si elle contient des paquets
  if (groups.nonClassee.length > 0) {
    out += `\n### ❓ Non classées (${groups.nonClassee.length})\n\n`;
    out += `| ${columns.join(' | ')} |\n`;
    out += `|${columns.map(() => '---').join('|')}|\n`;
    for (const r of groups.nonClassee) {
      out += `| ${r.name} | ${r.current} | ${r.latest} |\n`;
    }
  }

  return out;
}

// --- Composer : on exclut symfony/* ---
let composerItems = [];
try {
  const raw = fs.readFileSync('composer-outdated.json', 'utf8');
  const data = JSON.parse(raw);
  composerItems = (data.installed || [])
    .filter(pkg => !pkg.name.startsWith('symfony/'))
    .map(pkg => ({ name: pkg.name, current: pkg.version, latest: pkg.latest }));
} catch (e) {
  console.error('Erreur lecture composer-outdated.json:', e.message);
}

// --- Yarn ---
let yarnItems = [];
try {
  const raw = fs.readFileSync('yarn-outdated.json', 'utf8');
  const lines = raw.trim().split('\n').filter(Boolean).map(l => JSON.parse(l));
  const tableLine = lines.find(l => l.type === 'table');
  if (tableLine) {
    yarnItems = tableLine.data.body.map(row => ({
      name: row[0],
      current: row[1],
      latest: row[3],
    }));
  }
} catch (e) {
  console.error('Erreur lecture yarn-outdated.json:', e.message);
}

const columns = ['Paquet', 'Actuelle', 'Dernière'];

const report =
  `# 📋 Rapport dépendances obsolètes — ${new Date().toISOString().slice(0, 10)}\n\n` +
  `## 🎼 Back-end (Composer, hors Symfony)\n` +
  buildTables(composerItems, columns) +
  `\n---\n\n## 📦 Front-end (Yarn)\n` +
  buildTables(yarnItems, columns);

fs.writeFileSync('report_packages_template.md', report);
console.log(report);
