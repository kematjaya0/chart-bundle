# chart-bundle
- installation
  ```
  composer require kematjaya/chart-bundle
  ```
- usage
  - embed stylesheets
    ```
    {{ chart_stylesheet() }}
    ```
  - embed highchart javascript
    ```
    {{ chart_javascript() }}
    ```
  - render chart
    ```
    {{ render_chart() }}
    ```
  - create chart class
    ```
    <?php

    namespace App\Chart;

    use App\Entity\Example;
    use Doctrine\ORM\QueryBuilder;
    use Kematjaya\ChartBundle\Chart\AbstractChart;
    
    class ExampleChart extends AbstractChart
    {

        public function getTitle():string
        {
            return $this->translator->trans('examlple_chart');
        }

        public function getQueryBuilder(string $alias = 't', array $params = []): QueryBuilder
        {
            return $this->getEntityManager()->getRepository(Example::class)->createQueryBuilder($alias);
        }

        // category chart
        public function getCategories():array
        {
            return [
              'Jan', 'Feb', 'Mar', 'Apr'
            ];
        }

        public function getSeries(QueryBuilder $qb):array
        {
            $results = [];
            $rs = $qb->getQuery()->getResult();
            foreach (rs as $status) {
                $row = [
                    "name" => $status,
                    "data" => []
                ];

                foreach ($this->getCategories() as $label) {
                    $row['data'][] = rand();
                }

                $results[] = $row;
            }

            return $results;
        }

        public function getChartTitle():string
        {
            return sprintf("%s %s", $this->translator->trans('total'), $this->translator->trans('example'));
        }

        // if using table data 
        public function getDatas(QueryBuilder $qb): array 
        {
            $results = [];
            $rs = $qb->getQuery()->getResult();
            foreach ($this->getCategories() as $label) {

                $count = '<ul>';
                foreach ($rs as $status) {
                    $count .= sprintf("<li>%s: %s</li>", $this->translator->trans($status), rand());
                } 
                $count .= '<ul>';
                $results[] = [$this->translator->trans($label), $count];
            }

            return $results;
        }

        public function getHeaders(): array 
        {
            return [
               $this->translator->trans('month'), $this->translator->trans('total')
            ];
        }

        public function getSequence(): int 
        {
            return 1;
        }

    }

    ```
