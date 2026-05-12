// cookieUtil.js
export class CookieUtil {
  /**
   * Sets a cookie with the specified name, value, and expiration (default is 6 days).
   * @param {string} name
   * @param {string} value
   * @param {number} [days=6]
   */
  static setCookie( name, value, days = 6 ) {
    const expires = new Date();
    expires.setTime( expires.getTime() + days * 24 * 60 * 60 * 1000 );
    document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
  }

  /**
   * Retrieves a cookie value by its name.
   * @param {string} name
   * @return {string|null}
   */
  static getCookie( name ) {
    const value = `; ${document.cookie}`;
    const parts = value.split( `; ${name}=` );
    if ( parts.length === 2 ) return parts.pop().split( ';' ).shift();
    return null;
  }
}
