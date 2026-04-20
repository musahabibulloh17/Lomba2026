# Contributing Guidelines

## Selamat datang! 🎉

Terima kasih telah berkontribusi pada AI Workflow Automation project. Kami menghargai setiap kontribusi Anda!

## Cara Berkontribusi

### 1. Fork Repository

Fork repository ini ke akun GitHub Anda.

### 2. Clone Repository

```bash
git clone https://github.com/your-username/Lomba2026.git
cd Lomba2026
```

### 3. Buat Branch Baru

```bash
git checkout -b feature/nama-fitur-anda
# atau
git checkout -b fix/nama-bug-fix
```

### 4. Lakukan Perubahan

- Tulis kode yang bersih dan mudah dipahami
- Follow coding standards yang ada
- Tambahkan komentar jika diperlukan
- Update dokumentasi jika diperlukan

### 5. Commit Changes

```bash
git add .
git commit -m "feat: deskripsi singkat perubahan Anda"
```

**Commit Message Format:**
- `feat:` - Fitur baru
- `fix:` - Bug fix
- `docs:` - Perubahan dokumentasi
- `style:` - Formatting, semicolons, dll (tidak mengubah kode)
- `refactor:` - Refactoring kode
- `test:` - Menambah atau update tests
- `chore:` - Update build tasks, package manager, dll

### 6. Push ke GitHub

```bash
git push origin feature/nama-fitur-anda
```

### 7. Buat Pull Request

Buka Pull Request di GitHub dengan deskripsi yang jelas tentang perubahan Anda.

## Coding Standards

### JavaScript/Node.js

- Gunakan ES6+ syntax
- Gunakan async/await untuk asynchronous operations
- Gunakan const/let, hindari var
- Gunakan template literals untuk string interpolation
- Indent dengan 2 spaces
- Gunakan semicolons

### React

- Gunakan functional components dengan hooks
- Gunakan destructuring untuk props
- Nama file component dengan PascalCase
- Gunakan PropTypes atau TypeScript untuk type checking

### Naming Conventions

- **Variables & Functions:** camelCase (`getUserData`)
- **Classes & Components:** PascalCase (`UserProfile`)
- **Constants:** UPPER_SNAKE_CASE (`API_URL`)
- **Files:** kebab-case untuk utilities, PascalCase untuk components

## Testing

Pastikan semua tests pass sebelum submit PR:

```bash
# Backend tests
cd backend
npm test

# Frontend tests
cd frontend
npm test
```

## Pull Request Guidelines

### PR Title

Gunakan format yang sama dengan commit message:

```
feat: add task filtering by priority
fix: resolve meeting creation bug
docs: update API documentation
```

### PR Description

Include:
1. **What** - Apa yang diubah
2. **Why** - Kenapa perubahan ini diperlukan
3. **How** - Bagaimana perubahan ini bekerja
4. **Testing** - Bagaimana Anda menguji perubahan ini

### PR Checklist

- [ ] Kode sudah ditest
- [ ] Documentation sudah diupdate
- [ ] No linting errors
- [ ] Commit messages follow conventions
- [ ] PR description lengkap

## Code Review Process

1. Maintainer akan review PR Anda
2. Jika ada feedback, lakukan perubahan yang diminta
3. Setelah approved, PR akan di-merge

## Reporting Bugs

Gunakan GitHub Issues untuk melaporkan bugs dengan informasi:

1. **Deskripsi bug** - Jelaskan apa yang terjadi
2. **Steps to reproduce** - Bagaimana bug bisa terjadi
3. **Expected behavior** - Apa yang seharusnya terjadi
4. **Screenshots** - Jika applicable
5. **Environment** - OS, browser, Node version, dll

## Feature Requests

Untuk request fitur baru, buat GitHub Issue dengan:

1. **Use case** - Kenapa fitur ini diperlukan
2. **Proposed solution** - Bagaimana fitur ini seharusnya bekerja
3. **Alternatives** - Solusi alternatif yang sudah dipertimbangkan

## Questions?

Jika ada pertanyaan, silakan:
- Buka GitHub Issue dengan label "question"
- Contact maintainers

## License

Dengan berkontribusi, Anda setuju bahwa kontribusi Anda akan di-license under MIT License.

---

Terima kasih telah berkontribusi! 🙏
