#For start using the bundle:

##1. Add config file config.yml

```
async_table:
views: ~
```

##2. Create query and pass it to create table service

```
$query = $em->getRepository('...')->getQuery($request->query->get('filter'));

$tableData = [
    ['title' => 'Order ID', 'selector' => 'p.id', 'filter' => ['name' => 'order_id']],
    ['title' => 'Template ID', 'selector' => 't.id', 'filter' => [
        'name' => 'template_id',
        'type' => FilterMetadata::TYPE_SELECT,
        'options' => [5, 6],
        'empty_placeholder' => 'Select filter'
    ]],
    ['title' => 'Template Name'],
    ['title' => 'Status'],
    ['title' => 'Payment status'],
];

$table = $this->get('samsonos.async.table')->createTable('BackBundle:Handler/Order:table_content.html.twig', [
        'query' => $query,
        'page' => $request->query->get('page', 1)
    ], $tableData, [
        'isCompanyGroup' => $this->get('manager.domain')->isCompanyGroupMode()
    ]
);
```

Where BackBundle:Handler/Order:table_content.html.twig - is table content

```
% for entity in pagination %}
<tr data-user-id="{{ entity.id }}">
    {% set doc = entity.document.toArray() %}
    <td><span class="wrap-td" title="{{ entity.id }}">{{ entity.id }}</span></td>
    <td><span class="wrap-td" title="{{ entity.template.id }}">{{ entity.template.id }}</span></td>
    <td><span class="wrap-td" title="{{ doc[0].name }}">{{ doc[0].name }}</span></td>
    <td><span class="wrap-td" title="{{ entity.textStatus }}">{{ entity.textStatus }}</span></td>
    <td>...
```

##3. You can handle request from bundle js by:

```
if (null !== ($response = $this->get('samsonos.async.table')->handleContent($request, $table))) {
    return $response;
}
```

##4. And simply render you view:

```
return $this->render('BackBundle:Handler/Order:list.html.twig', [
    'table' => $table,
]);
```

##5. Use async_table in twig template for rendering the table

```
{{ async_table(table) }}
```

##6. Don't forget include js script

```
'@AsyncTableBundle/Resources/public/js/async-table.js'
```

Be happy:)