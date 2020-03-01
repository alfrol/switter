<?php


class QueryManager
{
    function __construct()
    {
    }

    /**
     * Handle the query.
     * Depending on the parameters provided by the query string
     * make according actions.
     *
     * @param string $query - Query string.
     */
    public static function handle_query(string $query) {
        $qm = new QueryManager();
        $key = explode('=', $query)[0];
        $value = explode('=', $query)[1];
        if ($key === 'logout' && $value === 'true') {
            $qm->handle_logout_query();
        } else if ($key === 'id') {
            $qm->handle_id_query($value);
        }
    }

    /**
     * Handle the query when the user wants to log out.
     */
    private function handle_logout_query() {
        session_unset();
        session_destroy();
        header('Location: login');
    }

    /**
     * Handle the query when the user id is provided.
     *
     * @param string $id - User id obtained from the query.
     */
    private function handle_id_query(string $id) {
        $db = new DBConnection();
        if ($db->exists($id,'id')) {
            $_SESSION['observable_user_id'] = $id;
        } else {
            header('Location: profile?id=' . $_SESSION['id']);
        }
    }
}
