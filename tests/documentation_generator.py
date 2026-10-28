"""Ensure documentation builds leave hand-maintained root pages untouched."""
from pathlib import Path
import os
import shutil
import subprocess
import tempfile
import unittest

ROOT = Path(__file__).resolve().parents[1]


class EditorialOwnershipTest(unittest.TestCase):
    def setUp(self):
        self.temporary = tempfile.TemporaryDirectory(prefix='guardian-docs-')
        self.addCleanup(self.temporary.cleanup)
        self.root = Path(self.temporary.name)
        for directory in ('tools/documentation', 'translations', 'docs/installation'):
            shutil.copytree(ROOT / directory, self.root / directory)
        target = self.root / 'src/I18n'
        target.mkdir(parents=True)
        shutil.copy2(ROOT / 'src/I18n/UiTranslator.php', target)
        shutil.copy2(ROOT / 'README.md', self.root)
        (self.root / 'examples').mkdir()
        self.editorial = {
            'SECURITY.md': '# Security Policy\r\n\r\nCustom policy — à conserver.\r\n'.encode(),
            'CHANGELOG.md': b'# Maintained release notes\n\nKeep this content.\n',
        }
        for name, content in self.editorial.items():
            (self.root / name).write_bytes(content)

    def generate(self):
        subprocess.run(
            [shutil.which('python3'), str(self.root / 'tools/documentation/generate.py')],
            check=True, capture_output=True, text=True, env=os.environ.copy(),
        )

    def assert_editorial_unchanged(self):
        for name, content in self.editorial.items():
            self.assertEqual((self.root / name).read_bytes(), content, name)

    def test_deleted_pages_stay_deleted_and_build_is_repeatable(self):
        self.generate()
        self.assert_editorial_unchanged()
        for name in ('CONTRIBUTING.md', 'CODE_OF_CONDUCT.md'):
            self.assertFalse((self.root / name).exists(), name)
            self.assertNotIn('../' + name, (self.root / 'docs/README.md').read_text())
        pages = {p.relative_to(self.root): p.read_bytes() for p in self.root.rglob('*.md')}
        self.generate()
        self.assertEqual(pages, {p.relative_to(self.root): p.read_bytes() for p in self.root.rglob('*.md')})
        self.assert_editorial_unchanged()

    def test_existing_editorial_pages_are_not_overwritten(self):
        for name in ('CONTRIBUTING.md', 'CODE_OF_CONDUCT.md'):
            self.editorial[name] = ('# Custom ' + name + '\n').encode()
            (self.root / name).write_bytes(self.editorial[name])
        self.generate()
        self.assert_editorial_unchanged()
        index = (self.root / 'docs/README.md').read_text()
        self.assertIn('../CONTRIBUTING.md', index)
        self.assertIn('../CODE_OF_CONDUCT.md', index)


if __name__ == '__main__':
    unittest.main()
