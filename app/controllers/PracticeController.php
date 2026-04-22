<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Practice;

class PracticeController extends Controller
{
    public function showPractice(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này.';
            $this->redirect($this->url('/login'));
            exit();
        }

        $userId = (int) $_SESSION['user_id'];
        $practiceModel = new Practice();

        $categories = $practiceModel->getCategories();
        $selectedCategoryId = (int) ($_GET['category'] ?? 0);

        if ($selectedCategoryId <= 0 && !empty($categories)) {
            $selectedCategoryId = (int) $categories[0]['id'];
        }

        $challenges = [];
        $selectedChallengeId = (int) ($_GET['challenge'] ?? 0);
        $selectedChallenge = null;

        if ($selectedCategoryId > 0) {
            $challenges = $practiceModel->getChallengesByCategory($selectedCategoryId, $userId);

            foreach ($challenges as $challengeItem) {
                if ((int) $challengeItem['id'] === $selectedChallengeId) {
                    $selectedChallenge = $challengeItem;
                    break;
                }
            }
        }

        $this->view('practice/practice', [
            'categories' => $categories,
            'selectedCategoryId' => $selectedCategoryId,
            'selectedChallengeId' => $selectedChallengeId,
            'selectedChallenge' => $selectedChallenge,
            'challenges' => $challenges,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null,
        ]);

        unset($_SESSION['error'], $_SESSION['success']);
    }

    public function submitFlag(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Bạn cần đăng nhập để thực hiện thao tác này.';
            $this->redirect($this->url('/login'));
            exit();
        }

        $userId = (int) $_SESSION['user_id'];
        $challengeId = (int) ($_POST['challenge_id'] ?? 0);
        $submittedFlag = trim((string) ($_POST['submitted_flag'] ?? ''));

        if ($challengeId <= 0 || $submittedFlag === '') {
            $_SESSION['error'] = 'Vui lòng chọn challenge và nhập flag hợp lệ.';
            $this->redirect($this->url('/practice'));
            return;
        }

        $practiceModel = new Practice();
        $challenge = $practiceModel->findActiveChallengeById($challengeId);

        if ($challenge === null) {
            $_SESSION['error'] = 'Challenge không tồn tại hoặc đã bị ẩn.';
            $this->redirect($this->url('/practice'));
            return;
        }

        $isCorrect = $practiceModel->verifyFlag($submittedFlag, (string) $challenge['flag_hash']);
        $practiceModel->createSubmission($userId, $challengeId, $submittedFlag, $isCorrect);

        if ($isCorrect) {
            $isNewSolve = $practiceModel->createSolve($userId, $challengeId);

            if ($isNewSolve) {
                $_SESSION['success'] = 'Chính xác! Bạn đã giải challenge "' . $challenge['title'] . '" và nhận ' . (int) $challenge['points'] . ' điểm.';
            } else {
                $_SESSION['success'] = 'Flag đúng. Challenge này bạn đã solve trước đó.';
            }
        } else {
            $_SESSION['error'] = 'Flag chưa đúng, thử lại nhé.';
        }

        $this->redirect($this->url('/practice?category=' . (int) $challenge['category_id'] . '&challenge=' . (int) $challengeId));
    }

    private function url(string $path): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $base = rtrim(dirname($scriptName), '/');

        if ($base === '' || $base === '.') {
            return $path;
        }

        return $base . $path;
    }

}
