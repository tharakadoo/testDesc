Clean Architecture

The Mechanism:

Explicit boundaries between layers (Domain → Application → Infrastructure)
Dependency Rule: Dependencies point inward only
Change to outer layer (UI, database) cannot break inner layer (business)
Blast radius of changes is contained



Use Cases:

Use cases are explicit, first-class objects
Business processes are visible in code structure
Use cases orchestrate domain logic without containing it
Changing business process = change use case, domain stays stable


Example:

// Use case structure makes business process explicit
class PlaceOrderUseCase {
constructor(
private orderRepository: OrderRepository,
private inventoryService: InventoryService,
private paymentGateway: PaymentGateway
) {}

execute(command: PlaceOrderCommand): void {
// Business process clearly orchestrated:
const order = this.orderRepository.findById(command.orderId);
order.place();  // Domain logic stays in domain

    this.inventoryService.reserve(order.items);
    this.paymentGateway.charge(order.totalAmount);

    this.orderRepository.save(order);
}
}

Test-Driven Development


The Mechanism:

Tests written first = specification of expected behavior
Tests run automatically on every change
Regression impossible without test failure
Confidence is measurable (test pass rate)
Example:

// Test defines expected behavior BEFORE implementation
describe('Order.place()', () => {
it('should throw error when order is empty', () => {
const order = new Order();

    expect(() => order.place())
      .toThrow(EmptyOrderException);
});

it('should emit OrderPlacedEvent when successful', () => {
const order = new Order();
order.addItem(product, 1);

    order.place();

    expect(order.events).toContain(OrderPlacedEvent);
});
});
