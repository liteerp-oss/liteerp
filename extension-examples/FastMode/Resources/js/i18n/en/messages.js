export default {
    fastmode: {
        title: "Fast Mode",
        subtitle: `Fastmode streamlines the order processing workflow by skipping the accounting approval step. 
        Under the standard process, an order is first reviewed operationally, then approved by accounting before being sent 
        to the warehouse. With Fastmode enabled, orders can move directly to the warehouse after the initial approval, 
        reducing delays and improving operational efficiency. 
        This feature is especially suitable for small businesses with simplified processes that prioritize speed and agility.`,
        submit: {
            label:  "Save changes"
        },
        form: {
            status: {
                label: "Status invoice default",
                paid: "Paid",
                pending: "Pending",
                partial_payment: "Partial payment"
            }
        },
        desc: `This mean you wanna invoice status to what's status? Fast Mode support for you skip step approve at Invoice module, 
                  it's useful for small business working with no accounting or they are handle manual or another place.`,
        success: {
            message: "You has been save changed"
        }
    },
    "fastmode-index":"View setting",
    "fastmode-create":"Save setting",
    "fastmode-test":"Send test"
}