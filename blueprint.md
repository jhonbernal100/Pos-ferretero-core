# POS Ferretero Blueprint

## Overview

POS Ferretero is a web-based Point of Sale (POS) system designed specifically for hardware stores in Colombia. The application helps store owners manage sales, inventory, and customer relationships, with a focus on streamlining operations and providing valuable business insights.

## Features

*   **Sales Management:** Create and manage sales transactions, including cash and credit sales. Document types include 'ticket', 'factura_electronica', 'abono_credito', and 'boleta'.
*   **Inventory Management:** Track product inventory, add new products, and manage stock levels.
*   **Customer Management:** Maintain a database of customers, including their contact information and purchase history.
*   **Credit Management:** Manage customer credit accounts, track balances, and record payments.
*   **Reporting:** Generate reports on sales, inventory, and customer activity.
*   **Trial System:** New users can sign up for a 30-day free trial by providing their business information (RUT).

## Design

The application will feature a clean and modern user interface with a focus on ease of use. The design will be responsive, ensuring a seamless experience across both desktop and mobile devices. Key design elements include:

*   **Color Palette:** A professional and inviting color scheme that reflects the hardware store theme.
*   **Typography:** Clear and legible fonts to enhance readability.
*   **Iconography:** Intuitive icons to guide users through the application's features.
*   **Layout:** A well-structured layout that prioritizes key information and actions.

## Current Request

**Request:** The user has provided the string "bia2026".

**Plan:**

1.  **Interpret Request:** Interpreted the request as adding a new document type 'boleta'.
2.  **Update Migration:** Modified the `database/migrations/2026_05_07_122233_add_abono_credito_to_ventas_table.php` file to add 'boleta' to the `tipo_documento` enum.
3.  **Document:** Updated this blueprint to reflect the change.
