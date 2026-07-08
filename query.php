SELECT 
    s.code,
    s.invoice_no,
    s.invoice_date,
    C.customer_name,
    C.shop_name,
    C.address,
    E.name AS sales_person_name,
    G.name AS sales_by_name,
    H.brunch AS branch_name,
    I.brunch AS dispatch_branch_name,
    COALESCE(SUM(si.sales_quantity * si.sales_rate), 0) AS invoice_price,
    s.discount,
    s.transport_cost,
    s.total_vat_cost,
    COALESCE(B.in_amount, 0) AS in_amount
FROM 
    sales_invoice s
INNER JOIN 
    sales_invoice_item si ON s.id = si.sales_invoice_id
LEFT JOIN 
    account_transection B ON s.transection_id = B.id
JOIN 
    setup_customer C ON s.customer_id = C.id
JOIN 
    admin D ON s.sales_person = D.id
JOIN 
    setup_employee E ON D.employee_id = E.id
JOIN 
    admin F ON s.sales_by = F.id
JOIN 
    setup_employee G ON F.employee_id = G.id
JOIN 
    setup_brunch H ON s.brunch_id = H.id
JOIN 
    setup_brunch I ON s.dispatch_from_which_brunch = I.id
    $QUERY
GROUP BY 
    s.invoice_no,
    s.code,
    s.invoice_date,
    C.customer_name,
    C.shop_name,
    C.address,
    E.name,
    G.name,
    H.brunch,
    I.brunch,
    s.discount,
    s.transport_cost,
    s.total_vat_cost,
    B.in_amount