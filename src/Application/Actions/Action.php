<?php
declare(strict_types=1);

namespace App\Application\Actions;

use App\Application\View\ViewInterface;
use App\Domain\DomainException\DomainRecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;
use Slim\Flash\Messages;

abstract class Action
{
    protected LoggerInterface $logger;

    protected ViewInterface $view;

    protected Request $request;

    protected Response $response;

    protected array $args;

    protected Messages $flash;

    public function __construct(LoggerInterface $logger, ViewInterface $view, Messages $flash)
    {
        $this->logger = $logger;
        $this->view = $view;
        $this->flash = $flash;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;

        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new HttpNotFoundException($this->request, $e->getMessage());
        } catch (HttpBadRequestException $e) {
            //TODO обработать правильно, пока пробрасываем наверх
            throw $e;
        }
    }

    /**
     * @return Response
     * @throws DomainRecordNotFoundException
     * @throws HttpBadRequestException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     * @throws HttpBadRequestException
     */
    protected function getJsonData()
    {
        $input = json_decode(file_get_contents('php://input'));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new HttpBadRequestException($this->request, 'Malformed JSON input.');
        }

        return $input;
    }

    /**
     * @return array|object
     * @throws HttpBadRequestException
     */
    protected function getFormData()
    {
        return ($this->request->getParsedBody());
    }


    /**
     * @param  string $name
     * @return mixed
     * @throws HttpBadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new HttpBadRequestException($this->request, "Could not resolve argument `{$name}`.");
        }

        return $this->args[$name];
    }

    /**
     * @param  array|object|null $data
     * @return Response
     */
    protected function respondWithData($data = null): Response
    {
        $payload = new ActionPayload(200, $data);
        return $this->respond($payload);
    }

    /**
     * @param ActionPayload $payload
     * @return Response
     */
    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);
        return $this->response->withHeader('Content-Type', 'application/json');
    }

    protected function setFormOldData(array $oldData):void
    {
        foreach ($oldData as $field => $value) {
            $this->flash->addMessage( $field  . '.old', $value);
        }
    }

    protected function setFormErrors(array $errors):void
    {
        foreach ($errors as $field => $value) {
            $this->flash->addMessage($field . '.error' , $value);
        }
    }

    protected function withRedirect(string $url):Response
    {
        return $this->response->withHeader('Location', $url);
    }
}
