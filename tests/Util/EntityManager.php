<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\ChartBundle\Tests\Util;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

/**
 * Description of EntityManager
 *
 * @author guest
 */
class EntityManager implements EntityManagerInterface 
{
    public function wrapInTransaction(callable $func): mixed
    {
        return null;
    }
    
    public function beginTransaction(): void 
    {
        
    }

    public function clear($objectName = null): void {
        
    }

    public function close(): void {
        
    }

    public function commit(): void {
        
    }

    public function contains($object): bool {
        
    }

    public function copy($entity, $deep = false): object {
        
    }

    public function createNamedNativeQuery($name): \Doctrine\ORM\NativeQuery {
        
    }

    public function createNamedQuery($name): \Doctrine\ORM\Query {
        
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm): \Doctrine\ORM\NativeQuery {
        
    }

    public function createQuery($dql = ''): \Doctrine\ORM\Query {
        
    }

    public function createQueryBuilder(): QueryBuilder 
    {
        return new QueryBuilder($this);
    }

    public function detach($object): void 
    {
        
    }

    public function find($className, $id) 
    {
        
    }

    public function flush(): void 
    {
        
    }

    public function getCache() 
    {
        
    }

    public function getClassMetadata($className): \Doctrine\Persistence\Mapping\ClassMetadata {
        
    }

    public function getConfiguration(): \Doctrine\ORM\Configuration {
        
    }

    public function getConnection(): \Doctrine\DBAL\Connection {
        
    }

    public function getEventManager(): \Doctrine\Common\EventManager {
        
    }

    public function getExpressionBuilder(): \Doctrine\ORM\Query\Expr {
        
    }

    public function getFilters(): \Doctrine\ORM\Query\FilterCollection {
        
    }

    public function getHydrator($hydrationMode): \Doctrine\ORM\Internal\Hydration\AbstractHydrator {
        
    }

    public function getMetadataFactory(): \Doctrine\Persistence\Mapping\ClassMetadataFactory {
        
    }

    public function getPartialReference($entityName, $identifier) {
        
    }

    public function getProxyFactory(): \Doctrine\ORM\Proxy\ProxyFactory {
        
    }

    public function getReference($entityName, $id) {
        
    }

    public function getRepository($className): \Doctrine\Persistence\ObjectRepository {
        
    }

    public function getUnitOfWork(): \Doctrine\ORM\UnitOfWork {
        
    }

    public function hasFilters(): bool {
        
    }

    public function initializeObject($obj): void {
        
    }

    public function isFiltersStateClean(): bool {
        
    }

    public function isOpen(): bool {
        
    }

    public function lock($entity, $lockMode, $lockVersion = null): void {
        
    }

    public function merge($object): object {
        
    }

    public function newHydrator($hydrationMode): \Doctrine\ORM\Internal\Hydration\AbstractHydrator {
        
    }

    public function persist($object): void {
        
    }

    public function refresh($object): void {
        
    }

    public function remove($object): void {
        
    }

    public function rollback(): void {
        
    }

    public function transactional($func): mixed {
        
    }
}
