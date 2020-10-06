<?php
declare(strict_types=1);

namespace App\Infrastructure\Service\Session;


class Session implements SessionInterface
{
    private string $name;
    private array $cookie;
    private int $timeToLive;

    public function __construct($time_to_live = 30, $name = "application.session", $cookie = [])
    {
        if ($this->isActive()) {
            return;
        }
        $this->timeToLive = $time_to_live;
        $this->name = $name;
        $this->cookie = $cookie;
        $this->cookie += [
            'lifetime' => 0,
            'path' => ini_get('session.cookie_path'),
            'domain' => ini_get('session.cookie_domain'),
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true
        ];
        if (!headers_sent()) {
            // Определяет, будет ли модуль использовать cookies для хранения идентификатора сессии на стороне клиента
            ini_set('session.use_cookies', '1');
            // Определяет, будет ли модуль использовать только cookies для хранения идентификатора сессии на стороне клиента
            ini_set('session.use_only_cookies', '1');
            session_set_cookie_params(
                $this->cookie['lifetime'],
                $this->cookie['path'],
                $this->cookie['domain'],
                $this->cookie['secure'],
                $this->cookie['httponly']
            );
        }
    }

    /**
     * Получение статуса активности сессии
     * @see https://secure.php.net/manual/en/function.session-status.php PHP session_status
     * @return bool Статус активности сессии
     */
    private function isActive():bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Получение идентификатора текущей сессии.
     * Метод является оберткой для реализации стандартного метода.
     * @see https://secure.php.net/manual/ru/function.session-id.php PHP session_id
     * @return string
     */
    private function getId()
    {
        return session_id();
    }

    /**
     * Получение имени сессии
     * Метод является оберткой для реализации стандартного метода
     * @see http://php.net/manual/ru/function.session-name.php PHP session_name
     * @return string|null
     */
    private function getName()
    {
        return $this->isActive() ? session_name() : null;
    }

    private function isValid()
    {
        return !$this->isExpired() && $this->isFingerprint();
    }

    /**
     * Проверка срока действия сессии
     * @return bool
     */
    private function isExpired()
    {
        $activity = isset($_SESSION['_last_activity']) ? $_SESSION['_last_activity'] : false;
        if ($activity && ((time() - $activity) > $this->timeToLive)) {
            return true;
        }
        $_SESSION['_last_activity'] = time();

        return false;
    }

    /**
     * Проверка клиента
     * @return bool
     */
    private function isFingerprint()
    {
        $hash = sha1($_SERVER['HTTP_USER_AGENT'] .
            (ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')));
        if (isset($_SESSION['_fingerprint'])) {
            return $_SESSION['_fingerprint'] === $hash;
        }
        $_SESSION['_fingerprint'] = $hash;
        return true;
    }

    /**
     * Назначение идентификатора текущей сессии.
     * Метод является оберткой для реализации стандартного метода.
     * @see https://secure.php.net/manual/ru/function.session-id.php PHP session_id
     * @param string $id Идентификатор сессии для текущей сессии
     */
    private function setId($id)
    {
        session_id($id);
    }

    /**
     * Установка имени сессии
     * Метод является оберткой для реализации стандартного метода
     * @see http://php.net/manual/ru/function.session-name.php PHP session_name
     * @param $name
     */
    public function setName($name)
    {
        if ($this->isActive()) {
            session_name($name);
        }
    }

    /**
     * Инициализация сессии
     */
    public function open()
    {
        if ($this->isActive()) {
            return;
        }

        session_start();
        if (!$this->isActive()) {
            throw new \RuntimeException('ошибка при создании сессии');
        }
    }

    public function destroy()
    {
        if ($this->isActive()) {
            $this->deleteAll();
        }
    }

    public function deleteAll():void
    {
        if ($this->isActive()) {
            $_SESSION = [];
        }
    }

    /**
     * Обновление текущего ID на новый. Метод имеет эффект только при наличии активной сессии.
     * @see https://secure.php.net/session_regenerate_id PHP session_regenerate_id
     * @param bool $delete_old_session
     */
    public function refresh($delete_old_session = true):void
    {
        if ($this->isActive()) {
            session_regenerate_id($delete_old_session);
        }
    }

    /**
     * Получение значение сессии по ключу.
     * @param string $key Ключ, по которому необходимо получить значения
     * @return null|mixed Значение сессии по ключу
     */
    public function get($key)
    {
        if ($this->isActive()) {
            return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
        }

        return null;
    }

    /**
     * Добавление или установка значений в сессию по ключу
     * @param string $key Ключ, в который необходимо добавить значения
     * @param string $value Значение добавления
     */
    public function set($key, $value):void
    {
        if ($this->isActive()) {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Удаление значения сессии по ключу
     * @param string $key Ключ, по которому необходимо удалить значения
     */
    public function delete($key):void
    {
        if ($this->isActive() && isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Проверка наличия ключа у сессии
     * @param string $key Ключ, в который необходимо найти
     * @return bool
     */
    public function hasKey($key):bool
    {
        return ($this->isActive() && isset($_SESSION[$key]));
    }
}