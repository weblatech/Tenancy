import { supabase } from './config.js';

/**
 * Core Database Service Module
 *
 * Provides a centralized, async layer for all Supabase queries.
 * Every function returns { data, error } for consistent error handling.
 * All queries are scoped to the 'public' schema.
 */

/**
 * Fetch all rows from a given table with optional filters.
 *
 * @param {string}  table       - Table name to query
 * @param {object}  options     - { select, filters, order, limit }
 * @returns {Promise<{data: Array|null, error: object|null}>}
 */
export async function fetchRows(table, { select = '*', filters = {}, order = null, limit = null } = {}) {
    let query = supabase.from(table).select(select);

    // Apply equality filters: { column: value }
    for (const [column, value] of Object.entries(filters)) {
        if (value !== undefined && value !== null) {
            query = query.eq(column, value);
        }
    }

    // Apply ordering
    if (order) {
        query = query.order(order.column, { ascending: order.ascending ?? false });
    }

    // Apply row limit
    if (limit) {
        query = query.limit(limit);
    }

    const { data, error } = await query;
    return { data, error };
}

/**
 * Fetch a single row by its primary key or a unique column.
 *
 * @param {string} table       - Table name
 * @param {object} match       - { column: value } to match
 * @param {string} select      - Columns to return (default '*')
 * @returns {Promise<{data: object|null, error: object|null}>}
 */
export async function fetchSingle(table, match, select = '*') {
    const { data, error } = await supabase
        .from(table)
        .select(select)
        .match(match)
        .single();

    return { data, error };
}

/**
 * Insert one or multiple rows into a table.
 *
 * @param {string}        table  - Table name
 * @param {object|Array}  rows   - Single row object or array of row objects
 * @returns {Promise<{data: Array|null, error: object|null}>}
 */
export async function insertRows(table, rows) {
    const { data, error } = await supabase
        .from(table)
        .insert(rows)
        .select();

    return { data, error };
}

/**
 * Update rows in a table matching a condition.
 *
 * @param {string} table   - Table name
 * @param {object} updates - Key-value pairs to set
 * @param {object} match   - { column: value } condition
 * @returns {Promise<{data: Array|null, error: object|null}>}
 */
export async function updateRows(table, updates, match) {
    const { data, error } = await supabase
        .from(table)
        .update(updates)
        .match(match)
        .select();

    return { data, error };
}

/**
 * Delete rows from a table matching a condition.
 *
 * @param {string} table - Table name
 * @param {object} match - { column: value } condition
 * @returns {Promise<{data: Array|null, error: object|null}>}
 */
export async function deleteRows(table, match) {
    const { data, error } = await supabase
        .from(table)
        .delete()
        .match(match)
        .select();

    return { data, error };
}

/**
 * Execute a raw RPC (Remote Procedure Call) on Supabase.
 *
 * @param {string} fnName    - PostgreSQL function name
 * @param {object} params    - Parameters to pass to the function
 * @returns {Promise<{data: any, error: object|null}>}
 */
export async function callFunction(fnName, params = {}) {
    const { data, error } = await supabase.rpc(fnName, params);
    return { data, error };
}

export default {
    fetchRows,
    fetchSingle,
    insertRows,
    updateRows,
    deleteRows,
    callFunction,
};
