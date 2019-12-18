<?php

namespace Drupal\digitalconvergence_utilities\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Provides utility methods for performing HTTP sub requests.
 */
trait MakeSubRequestTrait {

  /**
   * Makes a HTTP subrequest to retrieve a response.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The parent HTTP request.
   * @param string $url
   *   The path/url to which to make a subrequest for.
   * @param int $status_code
   *   The status code for the page being handled.
   *
   * @return \Drupal\Core\Render\HtmlResponse
   *   The response.
   *
   * @throws \Exception
   *   Throws an non-caught exceptions.
   */
  protected function makeSubrequest(Request $request, $url, $status_code) {
    $response = NULL;
    try {
      // Reuse the exact same request (so keep the same URL, keep the access
      // result, the exception, et cetera) but override the routing information.
      // This means that aside from routing, this is identical to the master
      // request. This allows us to generate a response that is executed on
      // behalf of the master request, i.e. for the original URL. This is what
      // allows us to e.g. generate a 404 response for the original URL; if we
      // would execute a subrequest with the 404 route's URL, then it'd be
      // generated for *that* URL, not the *original* URL.
      $sub_request = clone $request;

      // The routing to the 404 page should be done as GET request because it is
      // restricted to GET and POST requests only. Otherwise a DELETE request
      // would for example trigger a method not allowed exception.
      $request_context = clone ($this->accessUnawareRouter()->getContext());
      $request_context->setMethod('GET');
      $this->accessUnawareRouter()->setContext($request_context);

      $sub_request->attributes->add($this->accessUnawareRouter()->match($url));

      // Add to query (GET) or request (POST) parameters:
      // - 'destination' (to ensure e.g. the login form in a 403 response
      //   redirects to the original URL)
      $parameters = $sub_request->isMethod('GET') ? $sub_request->query : $sub_request->request;
      $parameters->add($this->redirectDestination()->getAsArray());

      $response = $this->httpKernel()->handle($sub_request, HttpKernelInterface::SUB_REQUEST);
      // Only 2xx responses should have their status code overridden; any
      // other status code should be passed on: redirects (3xx), error (5xx)…
      // @see https://www.drupal.org/node/2603788#comment-10504916
      if ($response->isSuccessful()) {
        $response->setStatusCode($status_code);
      }
    }
    catch (\Exception $exception) {
      throw $exception;
    }

    return $response;
  }

  /**
   * Returns an access unaware router.
   *
   * @return \Drupal\Core\Routing\Router
   *   The router.
   */
  protected function accessUnawareRouter() {
    return \Drupal::service('router.no_access_checks');
  }

  /**
   * Returns the redirect destination utility.
   *
   * @return \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected function redirectDestination() {
    return \Drupal::service('redirect.destination');
  }

  /**
   * Returns a stacked HTTP kernel.
   *
   * @return \Stack\StackedHttpKernel
   */
  protected function httpKernel() {
    return \Drupal::service('http_kernel');
  }

}
