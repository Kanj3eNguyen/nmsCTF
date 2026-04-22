USE nmsctf;

INSERT INTO categories (name, slug)
VALUES
    ('Web Exploitation', 'web'),
    ('Cryptography', 'crypto'),
    ('Forensics', 'forensics'),
    ('Binary Exploitation', 'pwn'),
    ('Misc', 'misc')
ON DUPLICATE KEY UPDATE
    name = VALUES(name);

INSERT INTO challenges (category_id, title, slug, description, points, difficulty, flag_hash, hint, file_path, is_active)
SELECT c.id, 'SQL Injection 101', 'web-sqli-101',
       'Login form has a SQLi flaw. Bypass auth and capture the flag.',
       100, 'easy', SHA2('nmsctf{web_sqli_basic}', 256),
       'Try payloads with quote balancing and comments.', NULL, 1
FROM categories c
WHERE c.slug = 'web'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    title = VALUES(title),
    description = VALUES(description),
    points = VALUES(points),
    difficulty = VALUES(difficulty),
    flag_hash = VALUES(flag_hash),
    hint = VALUES(hint),
    file_path = VALUES(file_path),
    is_active = VALUES(is_active);

INSERT INTO challenges (category_id, title, slug, description, points, difficulty, flag_hash, hint, file_path, is_active)
SELECT c.id, 'XSS Treasure Hunt', 'web-xss-treasure-hunt',
       'Stored XSS appears in the comment section. Trigger script to reveal hidden flag.',
       150, 'medium', SHA2('nmsctf{stored_xss_found_it}', 256),
       'Check rendering context and input sanitization gaps.', NULL, 1
FROM categories c
WHERE c.slug = 'web'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    title = VALUES(title),
    description = VALUES(description),
    points = VALUES(points),
    difficulty = VALUES(difficulty),
    flag_hash = VALUES(flag_hash),
    hint = VALUES(hint),
    file_path = VALUES(file_path),
    is_active = VALUES(is_active);

INSERT INTO challenges (category_id, title, slug, description, points, difficulty, flag_hash, hint, file_path, is_active)
SELECT c.id, 'Caesar Is Not Enough', 'crypto-caesar-not-enough',
       'A message is double-encrypted with a weak method. Recover plaintext and flag.',
       120, 'easy', SHA2('nmsctf{crypto_shift_and_reverse}', 256),
       'Think classic substitution and simple transformations.', NULL, 1
FROM categories c
WHERE c.slug = 'crypto'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    title = VALUES(title),
    description = VALUES(description),
    points = VALUES(points),
    difficulty = VALUES(difficulty),
    flag_hash = VALUES(flag_hash),
    hint = VALUES(hint),
    file_path = VALUES(file_path),
    is_active = VALUES(is_active);

INSERT INTO challenges (category_id, title, slug, description, points, difficulty, flag_hash, hint, file_path, is_active)
SELECT c.id, 'Corrupted PNG', 'forensics-corrupted-png',
       'A suspicious image fails to open fully. Repair the file and extract the embedded flag.',
       180, 'medium', SHA2('nmsctf{png_header_fixed}', 256),
       'Compare file signature with a valid PNG.', NULL, 1
FROM categories c
WHERE c.slug = 'forensics'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    title = VALUES(title),
    description = VALUES(description),
    points = VALUES(points),
    difficulty = VALUES(difficulty),
    flag_hash = VALUES(flag_hash),
    hint = VALUES(hint),
    file_path = VALUES(file_path),
    is_active = VALUES(is_active);

INSERT INTO challenges (category_id, title, slug, description, points, difficulty, flag_hash, hint, file_path, is_active)
SELECT c.id, 'Warmup Buffer Overflow', 'pwn-warmup-bof',
       'A tiny 32-bit binary has no stack canary. Control instruction pointer to print the flag.',
       220, 'hard', SHA2('nmsctf{ret2win_warmup}', 256),
       'Find offset and target function address.', NULL, 1
FROM categories c
WHERE c.slug = 'pwn'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    title = VALUES(title),
    description = VALUES(description),
    points = VALUES(points),
    difficulty = VALUES(difficulty),
    flag_hash = VALUES(flag_hash),
    hint = VALUES(hint),
    file_path = VALUES(file_path),
    is_active = VALUES(is_active);

INSERT INTO challenges (category_id, title, slug, description, points, difficulty, flag_hash, hint, file_path, is_active)
SELECT c.id, 'Hidden in Plain Sight', 'misc-hidden-in-plain-sight',
       'Clues are scattered in HTML comments, robots, and metadata. Chain them to get the flag.',
       90, 'easy', SHA2('nmsctf{read_everything}', 256),
       'Do not trust what is visible by default.', NULL, 1
FROM categories c
WHERE c.slug = 'misc'
ON DUPLICATE KEY UPDATE
    category_id = VALUES(category_id),
    title = VALUES(title),
    description = VALUES(description),
    points = VALUES(points),
    difficulty = VALUES(difficulty),
    flag_hash = VALUES(flag_hash),
    hint = VALUES(hint),
    file_path = VALUES(file_path),
    is_active = VALUES(is_active);
ALTER TABLE users
ADD COLUMN is_admin TINYINT(1) NOT NULL DEFAULT 0;